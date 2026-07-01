<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = [
        'title', 'description', 'required_skills', 'status'
    ];

    public function applications()
    {
        return $this->hasMany(OpportunityApplication::class);
    }
}
