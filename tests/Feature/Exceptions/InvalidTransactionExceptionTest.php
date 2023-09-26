<?php

namespace CraigPaul\Moneris\Tests\Feature\Exceptions;

use CraigPaul\Moneris\Exceptions\InvalidTransactionException;
use CraigPaul\Moneris\TestSupport\Cases\TestCase;
use CraigPaul\Moneris\Transaction;

class InvalidTransactionExceptionTest extends TestCase
{
    /** @test */
    public function getting_the_message(): void
    {
        $transaction = new Transaction(
            $this->gateway()->getConfig(),
            ['type' => 'not a valid type.'],
        );

        $this->assertFalse($transaction->valid());

        $exception = new InvalidTransactionException($transaction);

        $this->assertStringContainsString(
            'Unsupported transaction type',
            $exception->getMessage(),
        );
    }
}
