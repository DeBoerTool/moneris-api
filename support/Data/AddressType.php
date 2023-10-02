<?php

namespace CraigPaul\Moneris\Support\Data;

enum AddressType: string
{
    case Billing = 'billing';

    case Shipping = 'shipping';
}
