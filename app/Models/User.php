<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuperAdmin()
    {
        return (boolean)$this->super_admin;
    }

    public function hasPermission($permission = '')
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        $user_permissions = json_decode($this->user_permissions);
        if (in_array($permission, $user_permissions)) {
            return true;
        } else {
            return false;
        }
    }

    public static function makeListUser($selected_id = 0)
    {
        $users = User::where('state', 1)->where('id', '>', 1)->get(['id', 'username', 'name']);
        $html = "";
        foreach ($users as $user) {
            $selected = ($user->id == $selected_id) ? "selected" : "";
            $html .= "<option value=\"$user->id\" $selected>" . $user->name . "<" . $user->username . "></option>";
        }
        return $html;

    }

    public function getStatus()
    {
        if ($this->state == 1) {
            return "<p class='text-success mb-0'>Hoạt động</p>";
        }
        return "<p class='text-danger mb-0'>Đã khóa</p>";
    }
}
