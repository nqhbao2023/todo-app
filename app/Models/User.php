<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * // Thêm các property khác nếu muốn...
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable; 

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}
