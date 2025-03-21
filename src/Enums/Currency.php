<?php

namespace CraigPaul\Moneris\Enums;

/**
 * @see https://github.com/Moneris/Moneris-Gateway-API-PHP/blob/master/Moneris-Gateway-API-PHP-IG-v1.6.8.pdf
 */
enum Currency: string
{
	case USD = '840';
	case EUR = '978';
}
