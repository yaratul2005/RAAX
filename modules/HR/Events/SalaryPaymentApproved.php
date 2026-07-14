<?php

namespace Modules\HR\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\PayrollPayslip;

class SalaryPaymentApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PayrollPayslip $payslip;

    public function __construct(PayrollPayslip $payslip)
    {
        $this->payslip = $payslip;
    }
}
