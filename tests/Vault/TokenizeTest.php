<?php

namespace CraigPaul\Moneris\Tests\Vault;

use CraigPaul\Moneris\Data\Transactable\Preauth;
use CraigPaul\Moneris\Data\Transactable\Purchase;
use CraigPaul\Moneris\Data\Transactable\Tokenize;
use CraigPaul\Moneris\Support\Cards\CardInterface;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Transactables\VerifyCard;

class TokenizeTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function tokenizing_a_purchase(CardInterface $testCard): void
    {
        $orderId = $this->fixtures->orderId();

        $purchase = new Purchase(
            orderId: $orderId,
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
        );

        $transactionId = $this->submit($purchase)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        $tokenize = new Tokenize(
            orderId: $orderId,
            transactionId: $transactionId,
        );

        $this->submit($tokenize)
            ->isApproved('registered cc details')
            ->isSuccessful()
            ->isComplete();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function tokenizing_a_preauth(CardInterface $testCard): void
    {
        $orderId = $this->fixtures->orderId();

        $preauth = new Preauth(
            orderId: $orderId,
            creditCard: $testCard,
            amount: $this->fixtures->approvedAmount(),
        );

        $transactionId = $this->submit($preauth)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        $tokenize = new Tokenize(
            orderId: $orderId,
            transactionId: $transactionId,
        );

        $this->submit($tokenize)
            ->isApproved('registered cc details')
            ->isSuccessful()
            ->isComplete();
    }

    /**
     * @test
     *
     * @dataProvider basicCardProvider
     */
    public function tokenizing_a_verification(CardInterface $testCard): void
    {
        $orderId = $this->fixtures->orderId();

        $verify = new VerifyCard(
            orderId: $orderId,
            creditCard: $testCard,
        );

        $transactionId = $this->submit($verify)
            ->isSuccessful()
            ->isComplete()
            ->isApproved()
            ->getResponse()
            ->getTransactionId();

        $tokenize = new Tokenize(
            orderId: $orderId,
            transactionId: $transactionId,
        );

        $this->submit($tokenize)
            ->isApproved('registered cc details')
            ->isSuccessful()
            ->isComplete();
    }

}
