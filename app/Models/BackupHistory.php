<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BackupHistory extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'database_backups';

    protected $fillable = [
        'file_name',
        'status',
        'storage_disk',
        'storage_path',
        'storage_url',
        'size_bytes',
        'error_message',
        'patient_id',
        'patient_chat_id',
        'backup_date',
        'started_at',
        'completed_at',
        'expires_at',
        'whatsapp_sent_at',
        'whatsapp_response',
        'metadata',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'backup_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
        'whatsapp_response' => 'array',
        'metadata' => 'array',
    ];
}

