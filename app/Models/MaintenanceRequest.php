<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $table = 'maintenance_requests';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'requester_id',
        'request_type',
        'urgency',
        'item_name',
        'item_type',
        'color',
        'quantity',
        'installation_location',
        'external_destination',
        'status',
        'approved_by',
        'assigned_by',
        'purchaser_id',
        'gate_checker_id',
        'rejection_reason',
        'approved_at',
        'assigned_at',
        'purchased_at',
        'gate_checked_at',
        'received_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'assigned_at' => 'datetime',
        'purchased_at' => 'datetime',
        'gate_checked_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function purchaser()
    {
        return $this->belongsTo(Employee::class, 'purchaser_id');
    }
}
