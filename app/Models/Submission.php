<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'description', 
        'proof_file_path', 'status', 'points_awarded'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
