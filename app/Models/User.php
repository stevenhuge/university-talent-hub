<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'points',
        'bio',
        'major',
        'avatar'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Gamification Rank Logic
    public function getRankAttribute()
    {
        if ($this->points >= 300) return 'Legend';
        if ($this->points >= 150) return 'Gold';
        if ($this->points >= 50) return 'Silver';
        return 'Bronze';
    }
    
    public function getRankColorAttribute()
    {
        if ($this->points >= 300) return 'linear-gradient(135deg, #8b5cf6, #3b82f6)';
        if ($this->points >= 150) return 'linear-gradient(135deg, #fbbf24, #d97706)';
        if ($this->points >= 50) return 'linear-gradient(135deg, #94a3b8, #64748b)';
        return 'linear-gradient(135deg, #b45309, #78350f)';
    }
}
