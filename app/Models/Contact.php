<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'comment',
        'request_id',
        'ai_answer',
        'ai_category',
        'ai_sentiment',
        'ai_status',
        'ai_processed_at',
    ];

    protected function casts(): array
    {
        return [
            'ai_processed_at' => 'datetime',
        ];
    }
}
