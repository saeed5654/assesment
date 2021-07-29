<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_name', 'avatar', 'user_role', 'pin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function store($request)
    {
        return self::create([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'avatar' => $request->avatar_url,
            'user_role' => $request->user_role,
            'password' => bcrypt($request->password),
            'pin' => $request->pin
        ]);
    }

    /**
     * Handles phone and email verification code request
     *
     * @param $user
     */
    public static function verify($user)
    {
        $user->pin = '';
        $user->save();
    }

    /**
     * Create new user
     * @param $user
     * @param $request
     * @return mixed
     */
    public static function updateUser($user, $request)
    {
        $user->avatar = $request->avatar_url;
        $user->user_name = $request->user_name;
        $user->name = $request->name;
        return $user->save();
    }
}
