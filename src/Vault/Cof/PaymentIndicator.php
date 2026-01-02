<?php

namespace CraigPaul\Moneris\Vault\Cof;

/**
 * Indicates the current or intended use of the credentials
 *
 * Possible values for first transactions:
 * C - unscheduled Credential on File (first transactions only)
 * R – recurring
 *
 * Possible values for subsequent transactions:
 * R - recurring
 * U - unscheduled merchant-initiated transaction
 * Z - unscheduled customer-initiated transaction
 *
 * In Credential on File transactions where the request field e-commerce
 * indicator is also being sent: the allowable values for e-commerce indicator
 * are dependent on the value sent for payment indicator, as follows:
 *
 * if payment indicator = R, then allowable e-commerce indicator: 2, 5 or 6
 * if payment indicator = C, then allowable e-commerce indicator: 1, 5, 6 or 7
 * if payment indicator = U, then allowable e-commerce indicator: 1 or 7
 * if payment indicator = Z, then allowable e-commerce indicator: 1, 5, 6 or 7
 */
enum PaymentIndicator: string
{
	// Unscheduled Credential on File (first transactions only)
	case InitialUnscheduled = 'C';

	// Unscheduled merchant-initiated transaction
	case SubsequentUnscheduledMerchant = 'U';

	// Unscheduled customer-initiated transaction
	case SubsequentUnscheduledCustomer = 'Z';

	// Recurring, either initial or subsequent. Note that this value is not
	// compatible with the e-commerce indicator "7" aka SslEnabledMerchant,
	// which is the default value we use in this library.
	case Recurring = 'R';
}
