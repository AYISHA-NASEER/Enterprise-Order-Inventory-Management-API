<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ShipmentStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shipment $shipment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = match ($this->shipment->status) {
            'shipped' => 'Your order has been shipped.',
            'delivered' => 'Your order has been delivered.',
            default => 'Your shipment status has been updated.',
        };

        return [
            'message' => $message,
            'shipment_id' => $this->shipment->id,
            'order_id' => $this->shipment->order_id,
            'tracking_number' => $this->shipment->tracking_number,
            'carrier' => $this->shipment->carrier,
            'status' => $this->shipment->status,
        ];
    }
}