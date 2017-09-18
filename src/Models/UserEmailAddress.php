<?php

namespace Eightfold\Registered\Models;

use Illuminate\Database\Eloquent\Model;

use Validator;

use Illuminate\Database\Eloquent\Builder;

use Eightfold\Registered\Traits\BelongsToUserRegistration;

class UserEmailAddress extends Model
{
    use BelongsToUserRegistration;

    /**
     * We would like some attributes in the form
     * of a native class, which is not recognized
     * by the database, this should convert them.
     *
     */
    protected $casts = [
        'is_default' => 'boolean'
    ];

    protected $fillable = [
        'email', 'is_default', 'user_registration_id'
    ];

    static public function validatorPassed($email)
    {
        if (static::validator($email)->fails()) {
            return false;
        }
        return true;
    }

    static public function validator($email)
    {
        return Validator::make(['email' => $email], [
            'email' => static::validation()
        ]);
    }

    static public function validation()
    {
        return 'required|email|max:255|unique:user_email_addresses';
    }

    public function getAddressAttribute()
    {
        return $this->email;
    }

    /** Scopes */
    public function scopeWithAddress(Builder $query, string $address): Builder
    {
        return $query->where('email', $address);
    }
}
