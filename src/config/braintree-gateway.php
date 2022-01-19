<?php

return [
    'environment' => env('BRAINTREE_ENVIRONMENT', ''),
    'merchantId' => env('BRAINTREE_MERCHANT_ID', ''),
    'publicKey' => env('BRAINTREE_PUBLIC_KEY', ''),
    'privateKey' => env('BRAINTREE_PRIVATE_KEY', ''),


    /**
     * User model fields to create customer on Braintree server
     */
    'user_model' => [
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'company' => 'company',
        'email' => 'email',
        'phone' => 'mobile',
        'fax' => 'fax',
        'website' => 'website',
    ],


    'customer' => [
        'table' => 'braintree_customers',
        'model' => \NimaN2D\BraintreeGateway\Models\BraintreeCustomer::class,
    ],

    'credit_card' => [
        'table' => 'braintree_credit_cards',
        'model' => \NimaN2D\BraintreeGateway\Models\BraintreeCreditCard::class,
    ],

    'transactions' => [
        'table' => 'braintree_transactions',
        'model' => \NimaN2D\BraintreeGateway\Models\BraintreeTransaction::class,
    ]
];
