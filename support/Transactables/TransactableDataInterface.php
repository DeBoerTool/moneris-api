<?php

namespace CraigPaul\Moneris\Support\Transactables;

interface TransactableDataInterface
{
    /**
     * Get the data array, including normalized values, the type, and the
     * crypt_type. This is the final shape of the data which will be submitted
     * to the Moneris API.
     */
    public function getTransactableData(): array;
}
