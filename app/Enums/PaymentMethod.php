<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case Paypal = 'paypal';
    case BankTransfer = 'bank_transfer';
}
