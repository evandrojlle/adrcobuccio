<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{

    protected $table = 'wallet';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'owner_id',
        'amount',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Get comments relationship
     */
    public function banck_statements(): HasMany
    {
        return $this->hasMany(BankStatement::class, 'wallet_id', 'id');
    }
}
