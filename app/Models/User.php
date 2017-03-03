<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    
    protected $primaryKey = 'senderId';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'senderId', 'first_name', 'last_name', 'profile_pic', 'locale', 'timezone', 'gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
    
    /**
     * Route notifications for the Facebook channel.
     *
     * @return int
     */
    public function routeNotificationForFacebook()
    {
        return $this->senderId;
    }
}