<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','tag_id','name','number','is_favorite'];

    public function tag(){
        return $this->belongsTo(Tag::class);
    }
}
