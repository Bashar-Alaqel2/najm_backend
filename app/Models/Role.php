<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    public $timestamps = false;

    protected $fillable = [
        'name_ar',
        'description',
    ];

    public function operations()
    {
        return $this->belongsToMany(Operation::class, 'role_operations', 'role_id', 'operation_id');
    }
}
