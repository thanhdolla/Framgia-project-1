<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use willvincent\Rateable\Rateable;

class User extends Authenticatable
{
    use Rateable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'level_id',
        'birthday',
        'local_id',
        'address',
        'phone_number',
        'remove',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function responds()
    {
        return $this->hasMany(Respond::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function scopeCompare($query, $id)
    {
        if (Auth::user()->id == $id) {
            return true;
        }

        return false;
    }

    public function scopeSearch($query, $key)
    {
        return $query->where('name', 'ilike', '%' . $key . '%')->get()->toArray();
    }

    public function scopeRemove($query, $id)
    {
        return $query->where('id', $id)->update(['remove' => 1]);
    }

    public function scopeManager($query, $request)
    {
        return $query->where('name', 'ilike', '%' . $request . '%')->where('level_id', '<>', '3');
    }

    public function scopeMember($query, $request)
    {
        return $query->where('name', 'like', '%' . $request . '%')->where('level_id', '3');
    }
}

