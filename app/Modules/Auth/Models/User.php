<?php

namespace App\Modules\Auth\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const TYPE_ADMIN = 0;
    const TYPE_CUSTOMER = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Encode password
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            fn(string $value) => $value,
            fn(string $value) => bcrypt($value)
        );
    }

    /**
     * Removes access tokens.
     *
     * @param string $appName The application name
     * @return void
     */
    public function revokeExistingTokensFor(string $appName)
    {
        $this->tokens()->where([
            'name' => $appName,
            'revoked' => false,
        ])->update([
            'revoked' => true,
            'updated_at' => now(),
        ]);
    }

    protected function getIsAdminAttribute(){
        return $this->type == self::TYPE_ADMIN;
    }

    protected function getIsCustomerAttribute(){
        return $this->type == self::TYPE_CUSTOMER;
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
