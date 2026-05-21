<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'visitor_name',
        'identifier',
        'purpose',
        'id_image_url',
        'invitation_id',
        'entry_time',
        'exit_time',
        'status',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function invitation()
    {
        return $this->belongsTo(VisitorInvitation::class, 'invitation_id');
    }
}
