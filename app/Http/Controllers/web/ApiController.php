<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Person;

class ApiController extends Controller
{
    public function searchByUid($uid)
    {

        $person = Person::where('uid', $uid)->first();

        if ($person) {
            return response()->json($person, 200);
        }

        return response()->json(['message' => 'Person not found'], 404);
    }
}
