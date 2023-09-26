<?php

namespace CraigPaul\Moneris\Enums;

enum CofPaymentIndicator: string
{
    case Unscheduled = 'C';
    case Recurring = 'R';
    case RecurringVariable = 'V';
    case UnscheduledMerchantInitiated = 'U';
    case UnscheduledCustomerInitiated = 'Z';
}
