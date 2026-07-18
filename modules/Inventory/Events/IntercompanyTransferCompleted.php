<?php

namespace Modules\Inventory\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\IntercompanyTransfer;

class IntercompanyTransferCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public IntercompanyTransfer $transfer;

    public function __construct(IntercompanyTransfer $transfer)
    {
        $this->transfer = $transfer;
    }
}
