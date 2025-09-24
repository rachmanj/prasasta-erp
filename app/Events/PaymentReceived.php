<?php

namespace App\Events;

use App\Models\InstallmentPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public InstallmentPayment $payment
    ) {}
}
