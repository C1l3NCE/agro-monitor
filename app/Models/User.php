<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Field;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isAgronom()
    {
        return $this->role === 'agronom';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    //  ВОТ ЭТОГО МЕТОДА НЕ ХВАТАЛО
    public function hasRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function fields()
    {
        return $this->hasMany(\App\Models\Field::class);
    }

    public function conversations()
    {
        return Conversation::where('user_one', $this->id)
            ->orWhere('user_two', $this->id);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}