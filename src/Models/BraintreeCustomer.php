<?php

namespace NimaN2D\BraintreeGateway\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait IsBrainTreeCustomer.
 * @property int $customer_id
 */

class BraintreeCustomer extends Model
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
        'customer_id',
        'merchant_id',
        'global_id',
        'graphql_id'
    ];
}
