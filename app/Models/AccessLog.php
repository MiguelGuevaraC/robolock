<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'state', 'status', 'breakPoint',  'authorized_person_id'
    ];

    public function authorizedPerson()
    {
        return $this->belongsTo(Person::class);
    }
}
