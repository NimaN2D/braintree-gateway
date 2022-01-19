<?php

declare(strict_types=1);

namespace NimaN2D\BraintreeGateway\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use NimaN2D\BraintreeGateway\Facades\BraintreeGatewayFacade;
use NimaN2D\BraintreeGateway\Models\BraintreeCreditCard;
use NimaN2D\BraintreeGateway\Models\BraintreeCustomer;
use NimaN2D\BraintreeGateway\Models\BraintreeTransaction;
use NimaN2D\BraintreeGateway\Services\Interfaces\CastServiceInterface;
use Illuminate\Support\Collection;


/**
 * Trait IsBrainTreeCustomer.
 *
 * @property-read BraintreeCustomer $braintreeCustomer
 * @property-read BraintreeCreditCard $creditCards
 * @property int $braintree_customer_id
 *
 */
trait IsBrainTreeCustomer
{
    public function braintreeCustomer() : MorphOne
    {
        return app(CastServiceInterface::class)
            ->getCustomer($this)
            ->morphOne(config('braintree-gateway.customer.model',BraintreeCustomer::class),'holder');
    }

    public function creditCards() : MorphMany
    {
        return app(CastServiceInterface::class)
            ->getCustomer($this)
            ->morphMany(config('braintree-gateway.credit_card.model',BraintreeCreditCard::class),'holder');
    }

    public function transactions() : MorphMany
    {
        return app(CastServiceInterface::class)
            ->getCustomer($this)
            ->morphMany(config('braintree-gateway.credit_card.model',BraintreeTransaction::class),'holder');
    }

    public function createBraintreeCustomer() : void
    {
        braintreeCreateCustomer($this);
    }

    public function updateBraintreeCustomer() :void
    {
        braintreeUpdateCustomer($this);
    }

    public function getBraintreeToken() : string
    {
        return braintreeGetToken($this);
    }

    public function getBraintreeCustomerIdAttribute() : string
    {
        return (string) $this->braintreeCustomer->customer_id;
    }
}
