<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'citizen_feedback';

    protected $fillable = [
        'user_id',
        'subject',
        'anonymous',
        'contact_email',
        'message',
    ];

    protected $casts = [
        'anonymous' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


