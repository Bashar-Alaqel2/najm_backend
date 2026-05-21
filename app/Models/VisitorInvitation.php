<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorInvitation extends Model
{
    use HasFactory;

    protected $table = 'visitor_invitations';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'visitor_name',
        'inviter_id',
        'barcode_uuid',
        'expected_date',
        'is_used',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'is_used' => 'boolean',
    ];

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'invitation_id');
    }
}
