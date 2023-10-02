<?php

namespace CraigPaul\Moneris\Support\Http;

use CraigPaul\Moneris\Support\Xml\ToXmlInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpInterface
{
    public function post(
        string|ToXmlInterface $xml,
    ): ResponseInterface;
}
