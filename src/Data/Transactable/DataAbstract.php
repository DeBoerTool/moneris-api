<?php

namespace CraigPaul\Moneris\Data\Transactable;

use CraigPaul\Moneris\Enums\Crypt;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\DataInterface;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Support\Transactables\TransactableDataInterface;

abstract class DataAbstract implements TransactableDataInterface
{
    protected function __construct(
        public readonly array $data,
        public readonly TransactionType $type,
        public readonly Crypt $crypt = Crypt::SslEnabledMerchant,
        public readonly bool $useAvs = false,
        public readonly bool $useCvd = false,
        public readonly bool $useCof = false,
    ) {
    }

    public static function merge(
        array|DataInterface|TransactableDataInterface|null ...$items,
    ): array {
        $arrays = [];

        // Get an array representation of each of the items. If the item is
        // null, do nothing.
        foreach ($items as $item) {
            if (is_array($item)) {
                $arrays[] = $item;
            }

            if ($item instanceof DataInterface) {
                $arrays[] = $item->toArray();
            }

            if ($item instanceof TransactableDataInterface) {
                $arrays[] = $item->getTransactableData();
            }
        }

        // Merge all the arrays into a single array.
        return array_merge(...$arrays);
    }

    public function setFeatures(
        CredentialsInterface $config
    ): CredentialsInterface {
        return $config->withFeatures(
            useAvs: $this->useAvs,
            useCvd: $this->useCvd,
            useCof: $this->useCof,
        );
    }

    public function getTransactableData(): array
    {
        return [
            ...$this->getNormalized(),
            'type' => $this->type->value,
            'crypt_type' => $this->crypt->value,
        ];
    }

    protected function getNormalized(): array
    {
        $params = $this->data;

        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $params[$key] = trim($value);
            }

            if ($params[$key] === '') {
                unset($params[$key]);
            }
        }

        if (isset($params['credit_card'])) {
            $params['pan'] = preg_replace('/\D/', '', $params['credit_card']);
            unset($params['credit_card']);
        }

        if (isset($params['description'])) {
            $params['dynamic_descriptor'] = $params['description'];
            unset($params['description']);
        }

        if (isset($params['expiry_month']) && isset($params['expiry_year']) && !isset($params['expdate'])) {
            $params['expdate'] = sprintf('%02d%02d', $params['expiry_year'], $params['expiry_month']);
            unset($params['expiry_year'], $params['expiry_month']);
        }

        return $params;
    }
}
