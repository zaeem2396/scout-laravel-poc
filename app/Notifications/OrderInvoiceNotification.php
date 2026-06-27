<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderInvoiceNotification extends Notification implements ShouldQueue
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
        $lines = $this->order->items
            ->map(fn ($item) => $item->product->name.' × '.$item->quantity.' — $'.number_format((float) $item->total, 2))
            ->implode("\n");

        return (new MailMessage)
            ->subject('Invoice for order #'.$this->order->id)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Thank you for your purchase. Here is your invoice summary:')
            ->lines(explode("\n", $lines))
            ->line('Subtotal: $'.number_format((float) $this->order->subtotal, 2))
            ->line('Tax: $'.number_format((float) $this->order->tax, 2))
            ->line('Total: $'.number_format((float) $this->order->total, 2));
    }
}
