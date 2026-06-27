<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarehouseOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = $this->order->items->sum('quantity');

        return (new MailMessage)
            ->subject('Warehouse pick request for order #'.$this->order->id)
            ->line('A new order requires fulfillment.')
            ->line('Vendor: '.$this->order->vendor->name)
            ->line('Items to pick: '.$itemCount)
            ->line('Ship to: '.$this->order->user->name.' ('.$this->order->user->email.')');
    }
}
