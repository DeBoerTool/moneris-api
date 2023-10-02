<?php

namespace CraigPaul\Moneris\TestSupport\Data;

use CraigPaul\Moneris\Values\OrderId;
use CraigPaul\Moneris\Values\TransactionId;

class TransactionAndOrderIds
{
    public function __construct(
        public readonly TransactionId $transactionId,
        public readonly OrderId $orderId,
    )
    {
    }
}
