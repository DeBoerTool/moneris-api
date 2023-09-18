<?php

namespace CraigPaul\Moneris\Tests\Feature\Exceptions;

use CraigPaul\Moneris\Exceptions\InvalidTransactionException;
use CraigPaul\Moneris\Tests\Support\Stubs\GatewayStub;
use CraigPaul\Moneris\Tests\UnitTestCase;
use CraigPaul\Moneris\Transaction;

class InvalidTransactionExceptionTest extends UnitTestCase
{
    /** @test */
    public function getting_the_message(): void
    {
        $transaction = new Transaction(new GatewayStub(), [
            'type' => 'not a valid type.',
        ]);

        $this->assertFalse($transaction->valid());

        $exception = new InvalidTransactionException($transaction);

        $this->assertStringContainsString(
            'Unsupported transaction type',
            $exception->getMessage(),
        );
    }
}
