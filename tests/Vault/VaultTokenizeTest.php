<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Transactable\PurchaseData;
use CraigPaul\Moneris\Data\Transactable\TokenizeData;
use CraigPaul\Moneris\Interfaces\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;

class VaultTokenizeTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function tokenizing_a_transaction(CardInterface $testCard): void
    {
        $purchaseResponse = $this->gateway()->purchase(new PurchaseData([
            'order_id' => $this->uid(),
            'amount' => '1.00',
            ...$testCard->toArray(),
        ]));

        $tokenizeResponse = $this->gateway()->getVault()->tokenize(
            TokenizeData::fromTransaction($purchaseResponse->getTransaction()),
        );

        $this->assert->isSuccessful($tokenizeResponse);
        $this->assert->isComplete($tokenizeResponse);
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function tokenizing_with_customer_data(CardInterface $testCard): void
    {
        $purchaseResponse = $this->gateway()->purchase(new PurchaseData([
            'order_id' => $this->uid(),
            'amount' => '1.00',
            ...$testCard->toArray(),
        ]));

        $cardCustomerData = $this->fixtures->cardCustomerData();
        $tokenizeResponse = $this->gateway()->getVault()->tokenize(
            TokenizeData::fromTransaction(
                transaction: $purchaseResponse->getTransaction(),
                cardCustomerData: $cardCustomerData,
            ),
        );

        $this->assert->isSuccessful($tokenizeResponse);
        $this->assert->isComplete($tokenizeResponse);
        $this->assert->hasCardCustomer($tokenizeResponse, $cardCustomerData);
    }

    /**
     * @test
     *
     * @dataProvider avsCardProvider
     */
    public function tokenizing_with_avs_and_customer_data(CardInterface $testCard): void
    {
        $purchaseResponse = $this->gateway()->purchase(new PurchaseData([
            'order_id' => $this->uid(),
            'amount' => '1.00',
            ...$testCard->toArray(),
        ]));

        $cardCustomerData = $this->fixtures->cardCustomerData();
        $avsData = $this->fixtures->avsData();
        $tokenizeResponse = $this->gateway()->getVault()->tokenize(
            TokenizeData::fromTransaction(
                transaction: $purchaseResponse->getTransaction(),
                cardCustomerData: $cardCustomerData,
                avsData: $avsData
            ),
        );

        $this->assert->isSuccessful($tokenizeResponse);
        $this->assert->isComplete($tokenizeResponse);
        $this->assert->hasCardCustomer($tokenizeResponse, $cardCustomerData);
        $this->assert->hasAvsData($tokenizeResponse, $avsData);
    }
}
