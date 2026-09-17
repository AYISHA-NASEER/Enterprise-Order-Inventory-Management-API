<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Inventory $inventory
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert')
            ->line('A product has reached the low-stock level.')
            ->line('Product: ' . $this->inventory->product?->name)
            ->line('Current quantity: ' . $this->inventory->quantity)
            ->line('Product ID: ' . $this->inventory->product_id)
            ->line('Please check the inventory.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->inventory->product_id,
            'product_name' => $this->inventory->product?->name,
            'quantity' => $this->inventory->quantity,
        ];
    }
}