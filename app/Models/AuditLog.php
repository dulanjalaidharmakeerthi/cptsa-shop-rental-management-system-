<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_type',
        'actor_name',
        'actor_role',
        'actor_contact',
        'action',
        'auditable_type',
        'auditable_id',
        'auditable_name',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
