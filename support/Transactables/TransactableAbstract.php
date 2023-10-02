<?php

namespace CraigPaul\Moneris\Support\Transactables;

use CraigPaul\Moneris\Data\DataList;
use CraigPaul\Moneris\Enums\Crypt;
use CraigPaul\Moneris\Enums\TransactionType;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Support\Http\HttpInterface;
use CraigPaul\Moneris\Response;
use SimpleXMLElement;

class TransactableAbstract implements TransactableInterface
{
    public function __construct(
        public readonly DataList $data,
        public readonly TransactionType $type,
        public readonly Crypt $crypt = Crypt::SslEnabledMerchant,
    )
    {
    }

    public function submit(HttpInterface $http, CredentialsInterface $credentials): Response
    {
        $xml = $this->toXml($credentials);

//        var_dump($xml);

        return new Response($this, $http->post($xml));
    }

    public function addXml(SimpleXMLElement $element): void
    {
        $type = $element->addChild($this->type->value);

        $this->crypt->addXml($type);
        $this->data->addXml($type);
    }

    public function toXml(CredentialsInterface $credentials): string
    {
        $xml = new SimpleXMLElement('<request/>');

        $credentials->addXml($xml);
        $this->addXml($xml);

        return str_replace(' </', '</', $xml->asXML());
    }
}
