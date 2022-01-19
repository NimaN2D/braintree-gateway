<?php

namespace NimaN2D\BraintreeGateway;

class BraintreeConfigure
{
    /**
     * Indicates if Braintree gateway migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * BraintreeConfigure constructor.
     */
    final public function __construct()
    {
    }

    /**
     * Configure Braintree gateway to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations(): self
    {
        static::$runsMigrations = false;

        return new static();
    }
}
