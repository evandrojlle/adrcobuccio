<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatement extends Model
{

    protected $table = 'bank_statement';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'wallet_id',
        'transfer_id',
        'type_transaction',
        'amount_transaction',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transfer_id', 'id');
    }
}
