<?php

namespace NimaN2D\BraintreeGateway\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BraintreeTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holder_type',
        'holder_id',
        'braintree_id',
        'card_id',
        'currency_iso_code',
        'type',
        'amount',
        'status',
        'braintree_created_at',
        'braintree_updated_at',
    ];

    public function creditCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(config('braintree-gateway.credit_card.model',BraintreeCreditCard::class),'card_id','id');
    }
}
