<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Person",
 *     title="Person",
 *     type="object",
 *     required={"id", "typeofDocument", "documentNumber"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="typeofDocument", type="string", example="DNI"),
 *     @OA\Property(property="documentNumber", type="string", example="12345678"),
 *     @OA\Property(property="names", type="string", example="John"),
 *     @OA\Property(property="fatherSurname", type="string", example="Doe"),
 *     @OA\Property(property="motherSurname", type="string", example="Smith"),
 *     @OA\Property(property="businessName", type="string", example="Doe Enterprises"),
 *     @OA\Property(property="representativeDni", type="string", example="87654321"),
 *     @OA\Property(property="representativeNames", type="string", example="Jane Doe"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="origin", type="string", example="City"),
 *     @OA\Property(property="ocupation", type="string", example="Engineer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 * )
 */

class Person extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'typeofDocument', 'documentNumber', 'names', 'fatherSurname', 'motherSurname',
        'dateBirth', 'email', 'telephone', 'status', 'state'
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

}
