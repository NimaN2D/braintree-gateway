<?php

use NimaN2D\BraintreeGateway\Facades\BraintreeGatewayFacade;

if(!function_exists('braintreeGetToken'))
{
    function braintreeGetToken(\Illuminate\Database\Eloquent\Model $user = null) : ?string
    {
        $user = $user ?? auth()->user();
        if(!$user)
            return null;

        return BraintreeGatewayFacade::setUser($user)->getCustomerToken();
    }
}

if(!function_exists('braintreeCreateCustomer'))
{
    function braintreeCreateCustomer(\Illuminate\Database\Eloquent\Model $user = null) : ?\Braintree\Customer
    {
        $user = $user ?? auth()->user();
        if(!$user)
            return null;
        return BraintreeGatewayFacade::setUser($user)->createCustomer();
    }
}

if(!function_exists('braintreeUpdateCustomer'))
{
    function braintreeUpdateCustomer(\Illuminate\Database\Eloquent\Model $user = null) : ?\Braintree\Customer
    {
        $user = $user ?? auth()->user();
        if(!$user)
            return null;

        return BraintreeGatewayFacade::setUser($user)->updateCustomer();
    }
}

if(!function_exists('braintreeSale'))
{
    function braintreeSale(float $amount, string $nonce) : bool
    {

        return BraintreeGatewayFacade::sale($amount,$nonce);

    }
}

if(!function_exists('braintreeCheckTransaction'))
{
    function braintreeCheckTransaction(string $transaction_id) :bool
    {
        return BraintreeGatewayFacade::checkTransaction($transaction_id);
    }
}


