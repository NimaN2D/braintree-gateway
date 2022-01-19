<?php

declare(strict_types=1);

namespace NimaN2D\BraintreeGateway\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use NimaN2D\BraintreeGateway\Models\BraintreeCustomer;

interface CastServiceInterface
{

    /** @param Model|BraintreeCustomer $object */
    public function getCustomer($object): Model;

    public function getModel(object $object): Model;

}
