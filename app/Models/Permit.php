<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $table = 'permits';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'employee_id',
        'manager_id',
        'expected_exit_time',
        'expected_return_time',
        'actual_exit_time',
        'actual_return_time',
        'delay_minutes',
        'status',
        'reason',
    ];

    protected $casts = [
        'expected_exit_time' => 'datetime',
        'expected_return_time' => 'datetime',
        'actual_exit_time' => 'datetime',
        'actual_return_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
