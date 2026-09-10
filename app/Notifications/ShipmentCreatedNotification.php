<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ShipmentCreatedNotification extends Notification implements ShouldQueue
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
        return [
            'message' => 'Your shipment has been created and is being prepared.',
            'shipment_id' => $this->shipment->id,
            'order_id' => $this->shipment->order_id,
            'tracking_number' => $this->shipment->tracking_number,
            'carrier' => $this->shipment->carrier,
            'status' => $this->shipment->status,
        ];
    }
}