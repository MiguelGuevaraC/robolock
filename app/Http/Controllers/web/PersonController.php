<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    /**
     * Get all Persons
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active Persons",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Person")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $user = Auth::user();
        $typeUser = $user->typeUser;

        $accesses = $typeUser->getAccess($typeUser->id);

        $currentRoute = $request->path();
        $currentRouteParts = explode('/', $currentRoute);
        $lastPart = end($currentRouteParts);

        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            return view('Modulos.Student.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function all(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 15);
        $filters = $request->input('filters', []);

        $query = Person::where('state', 1)
            ->orderBy('id', 'desc');

        // Aplicar filtros por columna
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'documentNumber':
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('names', 'like', '%' . $searchValue . '%')
                                ->orWhere('fatherSurname', 'like', '%' . $searchValue . '%')
                                ->orWhere('motherSurname', 'like', '%' . $searchValue . '%')
                                ->orWhere('documentNumber', 'like', '%' . $searchValue . '%')
                                ->orWhere('identityNumber', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'id':
                        $query->where('id', 'like', '%' . $searchValue . '%');
                        break;
                    case 'level':
                        $query->where('level', 'like', '%' . $searchValue . '%');
                        break;
                    case 'grade':
                        $query->where('grade', 'like', '%' . $searchValue . '%');
                        break;
                    case 'section':
                        $query->where('section', 'like', '%' . $searchValue . '%');
                        break;
                    case 'representativeDni':
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('representativeNames', 'like', '%' . $searchValue . '%')
                                ->orWhere('representativeDni', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'telephone':
                        $query->where('telephone', 'like', '%' . $searchValue . '%');
                        break;
                }
            }
        }

        $totalRecords = $query->count();

        $list = $query->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $list,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/tecnimotors-backend/public/api/person",
     *      summary="Store a new person",
     *      tags={"Person"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"typeofDocument","documentNumber"},
     *              @OA\Property(property="typeofDocument", type="string", example="DNI", description="Type of Document"),
     *              @OA\Property(property="documentNumber", type="string", example="12345678", description="Document Number"),
     *              @OA\Property(property="names", type="string", example="John Doe", description="Names"),
     *              @OA\Property(property="fatherSurname", type="string", example="Doe", description="Father's Surname"),
     *              @OA\Property(property="motherSurname", type="string", example="Smith", description="Mother's Surname"),
     *              @OA\Property(property="businessName", type="string", example="Doe Enterprises", description="Business Name"),
     *              @OA\Property(property="representativeDni", type="string", example="87654321", description="Representative's DNI"),
     *              @OA\Property(property="representativeNames", type="string", example="Jane Doe", description="Representative's Names"),
     *              @OA\Property(property="address", type="string", example="123 Main St", description="Address"),
     *              @OA\Property(property="phone", type="string", example="+123456789", description="Phone Number"),
     *              @OA\Property(property="email", type="string", example="example@example.com", description="Email"),
     *              @OA\Property(property="origin", type="string", example="USA", description="Origin"),
     *              @OA\Property(property="ocupation", type="string", example="Engineer", description="Occupation")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Person created",
     *          @OA\JsonContent(ref="#/components/schemas/Person")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Person not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Person not found")
     *          )
     *      )
     * )
     */

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'typeofDocument' => 'required',
            'documentNumber' => [
                'required',
                Rule::unique('people')->whereNull('deleted_at'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'typeofDocument' => $request->input('typeofDocument'),
            'documentNumber' => $request->input('documentNumber'),
            'address' => $request->input('address') ?? null,
            'phone' => $request->input('phone') ?? null,
            'email' => $request->input('email') ?? null,
            'origin' => $request->input('origin') ?? null,
            'ocupation' => $request->input('ocupation') ?? null,
            'names' => null,
            'fatherSurname' => null,
            'motherSurname' => null,
            'businessName' => null,
            'representativeDni' => null,
            'representativeNames' => null,
        ];

        if ($request->input('typeofDocument') == 'DNI') {
            $data['names'] = $request->input('names') ?? null;
            $data['fatherSurname'] = $request->input('fatherSurname') ?? null;
            $data['motherSurname'] = $request->input('motherSurname') ?? null;
        } elseif ($request->input('typeofDocument') == 'RUC') {
            $data['businessName'] = $request->input('businessName') ?? null;
            $data['representativeDni'] = $request->input('representativeDni') ?? null;
            $data['representativeNames'] = $request->input('representativeNames') ?? null;
        }

        $object = Person::create($data);
        $object = Person::find($object->id);
        return response()->json($object, 200);

    }

    /**
     * Show the specified Person
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Person",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person found",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),
     * )
     */

    public function show(int $id)
    {

        $object = Person::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Person not found'], 404
        );

    }

    /**
     * @OA\Put(
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     summary="Update person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of person",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"typeofDocument","documentNumber"},
     *              @OA\Property(property="typeofDocument", type="string", example="DNI", description="Type of Document"),
     *              @OA\Property(property="documentNumber", type="string", example="12345678", description="Document Number"),
     *              @OA\Property(property="names", type="string", example="John Doe", description="Names"),
     *              @OA\Property(property="fatherSurname", type="string", example="Doe", description="Father's Surname"),
     *              @OA\Property(property="motherSurname", type="string", example="Smith", description="Mother's Surname"),
     *              @OA\Property(property="businessName", type="string", example="Doe Enterprises", description="Business Name"),
     *              @OA\Property(property="representativeDni", type="string", example="87654321", description="Representative's DNI"),
     *              @OA\Property(property="representativeNames", type="string", example="Jane Doe", description="Representative's Names"),
     *              @OA\Property(property="address", type="string", example="123 Main St", description="Address"),
     *              @OA\Property(property="phone", type="string", example="+123456789", description="Phone Number"),
     *              @OA\Property(property="email", type="string", example="example@example.com", description="Email"),
     *              @OA\Property(property="origin", type="string", example="USA", description="Origin"),
     *              @OA\Property(property="ocupation", type="string", example="Engineer", description="Occupation")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Person or User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),

     * )
     *
     */
    public function update(Request $request, string $id)
    {
        $object = Person::find($id);

        if (!$object) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'documentNumber' => [
                'required',
                Rule::unique('people')->ignore($object->id)->whereNull('deleted_at'),
            ],
            // Agrega aquí las reglas de validación para los demás campos que desees actualizar
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $object->documentNumber = $request->input('documentNumber');

        $object->names = $request->input('names');
        $object->fatherSurname = $request->input('fatherSurname');
        $object->motherSurname = $request->input('motherSurname');
        $object->businessName = $request->input('businessName');
        $object->level = $request->input('level');
        $object->grade = $request->input('grade');
        $object->section = $request->input('section');
        $object->representativeDni = $request->input('representativeDni');
        $object->representativeNames = $request->input('representativeNames');
        $object->telephone = $request->input('telephone');

        $object->save();
        $object = Person::find($object->id);

        return response()->json($object, 200);
    }

    /**
     * Remove the specified Person
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/person/{id}",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Person",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Person not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     ),

     * )
     *
     */
    public function destroy(int $id)
    {
        $object = Person::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Person not found'], 404
            );
        }
        //REVISAR ASOCIACIONES
        $object->delete();
    }

}
