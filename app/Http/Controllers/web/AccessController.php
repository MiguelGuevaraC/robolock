<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\GroupMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessController extends Controller
{

    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $typeUser = $user->typeUser;

        $accesses = $typeUser->getAccess($typeUser->id);

        $currentRoute = $request->path();
        $currentRouteParts = explode('/', $currentRoute);
        $lastPart = end($currentRouteParts);

        $totalAccessLog = AccessLog::where('state', 1);

        $totalAccessed = $totalAccessLog->count();
        $countAccessed = $totalAccessLog->where('status', 'Permitido')->count();
        $countNotAccessed = $totalAccessed - $countAccessed;

        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            return view('Modulos.Access.index', compact('user', 'groupMenu', 'groupMenuLeft', 'totalAccessed', 'countAccessed', 'countNotAccessed'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 20);

        $query = AccessLog::with(['authorizedPerson'])->where('state', 1);

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()');

                switch ($column['data']) {
                    case 'authorized_person.names':
                        $query->whereHas('authorizedPerson', function ($query) use ($searchValue) {
                            $query->where(function ($query) use ($searchValue) {
                                $query->where('names', 'like', '%' . $searchValue . '%')
                                    ->orWhere('fatherSurname', 'like', '%' . $searchValue . '%')
                                    ->orWhere('motherSurname', 'like', '%' . $searchValue . '%')
                                    ->orWhere('documentNumber', 'like', '%' . $searchValue . '%')
                                    ->orWhere('uid', 'like', '%' . $searchValue . '%');
                            });
                        });
                        break;

                    case 'authorized_person.email':
                        $query->whereHas('authorizedPerson', function ($query) use ($searchValue) {
                            $query->where('email', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'authorized_person.telephone':
                        $query->whereHas('authorizedPerson', function ($query) use ($searchValue) {
                            $query->where('telephone', 'like', '%' . $searchValue . '%');
                        });
                        break;

                    case 'status':
                        $query->where('status', 'like', '%' . $searchValue . '%');
                        break;

                    case 'breakPoint':
                        $query->where('breakPoint', 'like', '%' . $searchValue . '%');
                        break;

                    case 'created_at':
                        $query->whereDate('created_at', $searchValue);
                        break;
                }
            }
        }

        $totalRecords = AccessLog::where('state', 1)->count();
        $filteredRecords = $query->count();

        $list = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $countAccessed = $query->where('status', 'Permitido')->count();
        $countNotAccessed = $filteredRecords - $countAccessed;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'countAccessed' => $countAccessed,
            'countNotAccessed' => $countNotAccessed,
            'data' => $list,
        ]);
    }

    public function countRecords()
    {
        return AccessLog::where('state',1)->get()->count();
    }

}
