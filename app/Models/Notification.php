<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'id',
        'photoPath',
        'state',
        'status',
        'created_at',
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
}
