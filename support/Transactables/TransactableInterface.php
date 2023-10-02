<?php

namespace CraigPaul\Moneris\Support\Transactables;

use CraigPaul\Moneris\Support\Xml\AddXmlInterface;
use CraigPaul\Moneris\Support\Setup\CredentialsInterface;
use CraigPaul\Moneris\Support\Http\HttpInterface;
use CraigPaul\Moneris\Response;

interface TransactableInterface extends AddXmlInterface
{
    /**
     * Submit the transactable via the given HttpInterface instance. We require
     * credentials here as we need to convert the transactable to XML, and
     * valid XML requests must include the store ID and API token.
     */
    public function submit(
        HttpInterface $http,
        CredentialsInterface $credentials,
    ): Response;

    public function toXml(CredentialsInterface $credentials): string;
}
