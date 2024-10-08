<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'photoPath', 'state', 'status', 'person_id'
    ];

    public function authorizedPerson()
    {
        return $this->belongsTo(Person::class);
    }
}
