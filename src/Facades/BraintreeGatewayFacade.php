<?php

namespace NimaN2D\BraintreeGateway\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static BraintreeGateway setUser(\Illuminate\Database\Eloquent\Model $user)
 * @method static bool sale(float $amount, string $nonce)
 * @method static bool braintreeCheckTransaction(string $transaction_id)
 * @method static bool escrowTransaction(float $amount, string $nonce, \Illuminate\Database\Eloquent\Model $user = null)
 */

class BraintreeGatewayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'braintree_gateway';
    }
}
