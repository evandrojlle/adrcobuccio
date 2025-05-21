<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get wallet relationship
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'owner_id', 'id');
    }

    /**
     * Get transfers relationship
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(BankStatement::class, 'transfer_id', 'id');
    }

    /**
     * Get user by email
     * 
     * @param string $pEmail - The user email
     * @param bool $makeVisible - Defines whether the password should be visible.
     * @return array|self
     */
    public static function userByEmail(string $pEmail, bool $pMakeVisible = false): array|self
    {
        $fetchRow = self::query()
            ->filters(['email' => $pEmail])
            ->first();

        if (!$fetchRow) {
            return [];
        }

        if ($pMakeVisible) {
            $fetchRow->makeVisible(['password']);
        }


        return $fetchRow;
    }
}
