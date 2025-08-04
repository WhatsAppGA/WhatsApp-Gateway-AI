<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'body',
        'webhook',
        'status',
        'message_sent',
        'gemini_status',
        'gemini_model',
        'gemini_api_key',
        'gemini_instructions',
        'transcription_status',
        'transcription_model',
        'huggingface_api_key',
        'auto_status_save',
        'auto_status_forward',
        'status_nudity_detection',
        'chat_nudity_detection',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function autoreplies()
    {
        return $this->hasMany(Autoreply::class, 'device', 'body');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }


    
}
