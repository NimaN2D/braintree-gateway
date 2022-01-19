<?php

namespace NimaN2D\BraintreeGateway\Facades;

use App\Models\User;
use Braintree\Customer;
use Braintree\Exception\NotFound;
use Braintree\Gateway;
use Braintree\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Braintree\Transaction as BraintreeTransaction;
use NimaN2D\BraintreeGateway\Exceptions\BraintreeTransactionCouldNotHeldInScrew;
use NimaN2D\BraintreeGateway\Models\BraintreeCreditCard;
use NimaN2D\BraintreeGateway\Models\BraintreeTransaction as BraintreeTransactionModel;

class BraintreeGateway
{
    private Gateway $gateway;
    private User $user;
    private $transactionSuccessStatuses = [
        BraintreeTransaction::AUTHORIZED,
        BraintreeTransaction::AUTHORIZING,
        BraintreeTransaction::SETTLED,
        BraintreeTransaction::SETTLING,
        BraintreeTransaction::SETTLEMENT_CONFIRMED,
        BraintreeTransaction::SETTLEMENT_PENDING,
        BraintreeTransaction::SUBMITTED_FOR_SETTLEMENT
    ];

    public function __construct()
    {

        $this->gateway = new Gateway([
            'environment' => config('braintree-gateway.environment'),
            'merchantId' => config('braintree-gateway.merchantId'),
            'publicKey' => config('braintree-gateway.publicKey'),
            'privateKey' => config('braintree-gateway.privateKey')
        ]);

    }

    public function setUser(User $user): BraintreeGateway
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Create a customer on Braintree server
     */
    public function createCustomer(): ?Customer
    {

        if ($this->hasTrait())
            if ($this->getCustomerId())
                return $this->updateCustomer();

        /**
         * @var $response Customer
         */
        $response = $this->gateway->customer()->create([
            'firstName' => $user->{config('braintree-gateway.user_model.first_name')} ?? null,
            'lastName' => $user->{config('braintree-gateway.user_model.last_name')} ?? null,
            'company' => $user->{config('braintree-gateway.user_model.company')} ?? null,
            'email' => $user->{config('braintree-gateway.user_model.email')} ?? null,
            'phone' => $user->{config('braintree-gateway.user_model.mobile')} ?? null,
            'fax' => $user->{config('braintree-gateway.user_model.fax')} ?? null,
            'website' => $user->{config('braintree-gateway.user_model.website')} ?? null,
        ]);

        if ($this->hasTrait())
            $this->updateOrCreateUser($response->customer);

        return $response->success ? $response->customer : null;

    }

    public function updateCustomer(): ?Customer
    {

        $customer_id = $this->getCustomerId();

        if ($this->hasTrait())
            if (!$customer_id)
                return $this->createCustomer();

        $response = $this->gateway->customer()->update(
            $customer_id,
            [
                'firstName' => $this->user->{config('braintree-gateway.user_model.first_name')} ?? null,
                'lastName' => $this->user->{config('braintree-gateway.user_model.last_name')} ?? null,
                'company' => $this->user->{config('braintree-gateway.user_model.company')} ?? null,
                'email' => $this->user->{config('braintree-gateway.user_model.email')} ?? null,
                'phone' => $this->user->{config('braintree-gateway.user_model.mobile')} ?? null,
                'fax' => $this->user->{config('braintree-gateway.user_model.fax')} ?? null,
                'website' => $this->user->{config('braintree-gateway.user_model.website')} ?? null,
            ]
        );

        $this->updateOrCreateUser($response->customer);

        return $response->success ? $response->customer : null;

    }

    public function deleteCustomer(): bool
    {
        if (!$this->hasTrait() or !$customer_id = $this->getCustomerId())
            return false;

        return $this->gateway->customer()->delete($customer_id)->success;

    }

    /**
     * @throws InvalidArgumentException|string
     */
    public function getCustomerToken(): string
    {
        if (!$this->hasTrait() or !$customer_id = $this->getCustomerId())
            return false;

        return $this->gateway->clientToken()->generate([
            'customerId' => $customer_id
        ]);

    }

    public function sale(float $amount, string $payment_method_nonce): bool
    {
        $result = $this->gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $payment_method_nonce,
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        if ($result->success || !is_null($result->transaction)) {
            $this->recordTransaction($result->transaction);
            return true;
        } else {
            return false;
        }
    }

    public function checkTransaction(string $transaction_id): bool
    {
        $transaction = $this->gateway->transaction()->find($transaction_id);
        if (in_array($transaction->status, $this->transactionSuccessStatuses))
            return true;

        return false;
    }

    private function hasTrait(): bool
    {
        return Arr::has(class_uses_recursive($this->user), 'NimaN2D\BraintreeGateway\Traits\IsBrainTreeCustomer');
    }

    private function getCustomerId(): ?string
    {
        if ($this->user->braintreeCustomer()->exists())
            return $this->user->braintree_customer_id;

        return null;
    }

    private function recordTransaction(Transaction $transaction): void
    {

        $card = $this->recordCreditCard($transaction);
        $card->transactions()->create([
            'holder_type' => $card->holder_type,
            'holder_id' => $card->holder_id,
            'braintree_id' => $transaction->id,
            'type' => $transaction->type,
            'currency_iso_code' => $transaction->currencyIsoCode,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
            'braintree_created_at' => Carbon::parse($transaction->createdAt)->toDateTimeString(),
            'braintree_updated_at' => Carbon::parse($transaction->updatedAt)->toDateTimeString(),
        ]);

    }

    private function recordCreditCard(Transaction $transaction): BraintreeCreditCard
    {

        /**@var BraintreeCreditCard $card */
        $card = $this->user->creditCards()->create([
            'token' => $transaction->creditCardDetails->token,
            'card_type' => $transaction->creditCardDetails->cardType,
            'bin' => $transaction->creditCardDetails->bin,
            'last_four' => $transaction->creditCardDetails->last4,
            'card_holder' => $transaction->creditCardDetails->cardholderName,
            'expiration_date' => \Carbon\Carbon::create($transaction->creditCardDetails->expirationYear, $transaction->creditCardDetails->expirationMonth)->firstOfMonth()->startOfDay()->toDateTimeString(),
        ]);

        return $card;

    }

    private function updateOrCreateUser(Customer $response): void
    {
        if ($this->hasTrait())
            $this->user->braintreeCustomer()->updateOrCreate(
                [
                    'customer_id' => $response->id,
                ],
                [
                    'merchant_id' => $response->merchantId,
                    'global_id' => $response->globalId,
                    'graphql_id' => $response->graphQLId
                ]);
    }

}
