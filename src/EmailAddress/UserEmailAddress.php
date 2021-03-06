<?php

namespace Eightfold\Registered\EmailAddress;

use Illuminate\Database\Eloquent\Model;

use Validator;

use Illuminate\Database\Eloquent\Builder;

use Eightfold\Registered\Framework\Traits\BelongsToUserRegistration;

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

    public function siblings()
    {
        return static::where('user_registration_id', $this->user_registration_id)
            ->where('id', '<>', $this->id)->get();
    }

    /** Overrides */
    public function delete()
    {
        if ($this->siblings()->count() == 0) {
            return false;
        }
        return parent::delete();
    }

    /** Scopes */
    public function scopeIsDefault(Builder $query, bool $default = true): Builder
    {
        return $query->where('is_default', $default);
    }

    public function scopeWithAddress(Builder $query, string $address): Builder
    {
        return $query->where('email', $address);
    }
}
