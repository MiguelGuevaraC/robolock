<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\Person;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            if ($column['searchable'] === 'true' && !empty($column['search']['value'])) {
                $searchValue = trim($column['search']['value'], '()'); // Quitar paréntesis adicionales

                switch ($column['data']) {
                    case 'documentNumber':
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('names', 'like', '%' . $searchValue . '%')
                                ->orWhere('names', 'like', '%' . $searchValue . '%')
                                ->orWhere('fatherSurname', 'like', '%' . $searchValue . '%')
                                ->orWhere('motherSurname', 'like', '%' . $searchValue . '%')
                                ->orWhere('documentNumber', 'like', '%' . $searchValue . '%')
                                ->orWhere('uid', 'like', '%' . $searchValue . '%');
                        });
                        break;
                    case 'id':
                        $query->where('id', 'like', '%' . $searchValue . '%');
                        break;
                    case 'dateBirth':
                        $query->where('dateBirth', 'like', '%' . $searchValue . '%');
                        break;
                    case 'email':
                        $query->where('email', 'like', '%' . $searchValue . '%');
                        break;

                }
            }
        }

        $totalRecordsFiltered = $query->count();

        $list = $query->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => Person::where('state', 1)->count(),
            'recordsFiltered' => $totalRecordsFiltered,
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

        // Validación de los campos
        $messages = [
            'documentNumber.required' => 'El número de documento es obligatorio.',
            'documentNumber.string' => 'El número de documento debe ser una cadena de caracteres.',
            'documentNumber.unique' => 'El número de documento ya existe.',
            'names.required' => 'El nombre es obligatorio.',
            'names.string' => 'El nombre debe ser una cadena de caracteres.',
            'names.max' => 'El nombre no puede tener más de 255 caracteres.',
            'fatherSurname.required' => 'El apellido paterno es obligatorio.',
            'fatherSurname.string' => 'El apellido paterno debe ser una cadena de caracteres.',
            'fatherSurname.max' => 'El apellido paterno no puede tener más de 255 caracteres.',
            'motherSurname.required' => 'El apellido materno es obligatorio.',
            'motherSurname.string' => 'El apellido materno debe ser una cadena de caracteres.',
            'motherSurname.max' => 'El apellido materno no puede tener más de 255 caracteres.',
            'UID.required' => 'El UID es obligatorio.',
            'UID.string' => 'El UID debe ser una cadena de caracteres.',
            'UID.max' => 'El UID no puede tener más de 255 caracteres.',
            'photos.*.image' => 'Cada foto debe ser una imagen.',
            'photos.*.mimes' => 'Cada foto debe estar en formato jpg, jpeg o png.',
            'photos.*.max' => 'Cada foto no puede superar los 2048 KB.',
            'email.string' => 'El correo electrónico debe ser una cadena de caracteres.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo válida.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'telefono.string' => 'El teléfono debe ser una cadena de caracteres.',
            'telefono.regex' => 'El teléfono debe contener exactamente 9 dígitos.',
        ];

        $validator = validator()->make($request->all(), [
            'documentNumber' => [
                'required',
                'string',
                Rule::unique('people')->whereNull('deleted_at'),
            ],
            'names' => 'required|string|max:255',
            'fatherSurname' => 'required|string|max:255',
            'motherSurname' => 'required|string|max:255',
            'UID' => 'required|string|max:255',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048', // Validación de las fotos
            'email' => 'nullable|string|email|max:255',
            'telefono' => [
                'nullable',
                'string',
                'regex:/^\d{9}$/',
            ],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Datos de la persona
        $data = [
            'typeofDocument' => 'DNI',
            'documentNumber' => $request->input('documentNumber'),
            'names' => $request->input('names'),
            'fatherSurname' => $request->input('fatherSurname'),
            'motherSurname' => $request->input('motherSurname'),
            'uid' => $request->input('UID'),
            'status' => 'Activo',
            'email' => $request->input('email'),
            'telephone' => $request->input('telefono'),
            'dateBirth' => $request->input('dateOfBirth'),
        ];

        // Creación del objeto Person
        $person = Person::create($data);

        // Manejo de las fotos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {

                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/photos/' . $person->id, $filename);
                Storage::chmod($filePath, 0777);
                Photo::create([
                    'person_id' => $person->id,
                    'photoPath' => Storage::url('app/public/photos/' . $person->id . '/' . $filename),
                    'status' => 'Activo',
                ]);
            }
        }
        $person = Person::find($person->id);

        // Devolver la respuesta con el objeto creado
        return response()->json($person, 200);
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

        $object = Person::with(['photos', 'accessLogs'])->find($id);
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
    public function update(Request $request, $id)
    {

        // Validación de los campos
        $messages = [
            'documentNumber.string' => 'El número de documento debe ser una cadena de caracteres.',
            'documentNumber.required' => 'El número de documento es obligatorio.',
            'documentNumber.unique' => 'El número de documento ya existe.',
            'namesE.required' => 'El nombre es obligatorio.',
            'namesE.string' => 'El nombre debe ser una cadena de caracteres.',
            'namesE.max' => 'El nombre no puede tener más de 255 caracteres.',
            'fatherSurnameE.required' => 'El apellido paterno es obligatorio.',
            'fatherSurnameE.string' => 'El apellido paterno debe ser una cadena de caracteres.',
            'fatherSurnameE.max' => 'El apellido paterno no puede tener más de 255 caracteres.',
            'motherSurnameE.required' => 'El apellido materno es obligatorio.',
            'motherSurnameE.string' => 'El apellido materno debe ser una cadena de caracteres.',
            'motherSurnameE.max' => 'El apellido materno no puede tener más de 255 caracteres.',
            'UIDE.required' => 'El UID es obligatorio.',
            'UIDE.string' => 'El UID debe ser una cadena de caracteres.',
            'UIDE.max' => 'El UID no puede tener más de 255 caracteres.',
            'photosEd.*.image' => 'Cada foto debe ser una imagen.',
            'photosEd.*.mimes' => 'Cada foto debe estar en formato jpg, jpeg o png.',
            'photosEd.*.max' => 'Cada foto no puede superar los 2048 KB.',
            'emailE.string' => 'El correo electrónico debe ser una cadena de caracteres.',
            'emailE.email' => 'El correo electrónico debe ser una dirección de correo válida.',
            'emailE.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'telefonoE.string' => 'El teléfono debe ser una cadena de caracteres.',
            'telefonoE.regex' => 'El teléfono debe contener exactamente 9 dígitos.',
        ];

        $validator = validator()->make($request->all(), [
            'documentNumber' => [
                'required',
                'string',
                Rule::unique('people') // Nombre del campo en la base de datos
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'namesE' => 'required|string|max:255',
            'fatherSurnameE' => 'required|string|max:255',
            'motherSurnameE' => 'required|string|max:255',
            'UIDE' => 'required|string|max:255',
            'photosEd.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'emailE' => 'nullable|string|email|max:255',
            'telefonoE' => [
                'nullable',
                'string',
                'regex:/^\d{9}$/',
            ],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Encontrar la persona
        $person = Person::find($id);
        if (!$person) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        // Actualizar datos de la persona
        $person->update([
            'documentNumber' => $request->input('documentNumber'),
            'names' => $request->input('namesE'),
            'fatherSurname' => $request->input('fatherSurnameE'),
            'motherSurname' => $request->input('motherSurnameE'),
            'uid' => $request->input('UIDE'),
            'status' => 'Activo',
            'email' => $request->input('emailE'),
            'telephone' => $request->input('telefonoE'),
            'dateBirth' => $request->input('dateOfBirthE'), // Asumiendo que también necesitas agregar "E" aquí
        ]);

        $oldPhotos = Photo::where('person_id', $person->id)->get();
        // Eliminar fotos antiguas de la carpeta y de la base de datos
        $directoryPath = 'public/photos/' . $person->id;

        // Eliminar todas las fotos en el directorio
        if (Storage::exists($directoryPath)) {
            // Elimina todos los archivos en el directorio
            $files = Storage::files($directoryPath);
            foreach ($files as $file) {
                Storage::delete($file);
            }

            // Elimina el directorio después de borrar los archivos
            Storage::deleteDirectory($directoryPath);
        }

        // Elimina las entradas en la base de datos
        foreach ($oldPhotos as $photo) {
            $photo->delete();
        }
        // Manejo de las nuevas fotos
        if ($request->hasFile('photosEd')) {
            foreach ($request->file('photosEd') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/photos/' . $person->id, $filename);

                // Asegúrate de que el directorio tenga permisos adecuados
                $directoryPath = storage_path('app/public/photos/' . $person->id);
                if (!is_dir($directoryPath)) {
                    mkdir($directoryPath, 0777, true); // Crear directorio con permisos 0777
                }

                // Asegúrate de que el archivo tenga permisos adecuados
                $absoluteFilePath = storage_path('/app/public/photos/' . $person->id . '/' . $filename);
                chmod($absoluteFilePath, 0777);

                Photo::create([
                    'person_id' => $person->id,
                    'photoPath' => Storage::url('/app/public/photos/' . $person->id . '/' . $filename),
                    'status' => 'Activo',
                ]);
            }
        }

        // Aplicar permisos recursivos a la carpeta
        $absoluteDirectoryPath = storage_path('app/public/photos/' . $person->id);
        $this->recursiveChmod($absoluteDirectoryPath, 0777);

        // Devolver la respuesta con el objeto actualizado
        return response()->json($person, 200);
    }

    private function recursiveChmod($path, $permissions)
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            chmod($item->getPathname(), $permissions);
            if ($item->isDir() && !$item->isDot()) {
                $this->recursiveChmod($item->getPathname(), $permissions);
            }
        }
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
        $object->state = 0;
        $object->save();
    }

}
