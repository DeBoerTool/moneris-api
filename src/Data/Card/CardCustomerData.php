<?php

namespace CraigPaul\Moneris\Data\Card;

use CraigPaul\Moneris\Interfaces\DataInterface;
use CraigPaul\Moneris\Traits\PreparableTrait;

/**
 * The customer data that can be attached to Vault transactions is different
 * from the customer data that can be attached to Purchase transactions.
 */
class CardCustomerData implements DataInterface
{
    use PreparableTrait;

    public function __construct(
        public readonly string $customerId,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $note,
    ) {
    }

    public function toArray(): array
    {
        return [
            'cust_id' => $this->customerId,
            'email' => $this->email,
            'phone' => $this->phone,
            'note' => $this->note,
        ];
    }

    /**
     * Get the customer data formatted for a purchase transaction.
     */
    public function getPurchaseArray(): array
    {
        return [
            'cust_id' => $this->customerId,
            'cust_info' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'note' => $this->note,
            ],
        ];
    }
}
