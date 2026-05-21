<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'operations';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name_ar',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_operations', 'operation_id', 'role_id');
    }
}
