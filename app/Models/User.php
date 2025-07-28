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
  * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar_url* 
 * @property string $role
 * // Thêm các property khác nếu muốn...
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable; 

    protected $fillable = ['name', 'email', 'password', 'role', 'avatar_url'];
    protected $hidden = ['password'];

    // Hàm tiện ích kiểm tra role
    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isLeader() {
        return $this->role === 'leader';
    }

    public function isMember() {
        return $this->role === 'member';
    }
}
