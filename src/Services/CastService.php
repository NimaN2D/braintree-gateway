<?php

declare(strict_types=1);

namespace NimaN2D\BraintreeGateway\Services;

use Illuminate\Database\Eloquent\Model;
use NimaN2D\BraintreeGateway\Models\BraintreeCustomer;
use NimaN2D\BraintreeGateway\Services\Interfaces\CastServiceInterface;

/** @psalm-internal */
final class CastService implements CastServiceInterface
{

    /** @param Model|BraintreeCustomer $object */
    public function getCustomer($object): Model
    {
        return $this->getModel($object instanceof BraintreeCustomer ? $object->holder : $object);
    }

    public function getModel(object $object): Model
    {
        assert($object instanceof Model);

        return $object;
    }

}
