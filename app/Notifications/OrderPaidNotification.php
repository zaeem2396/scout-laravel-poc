<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order #'.$this->order->id.' payment confirmed')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your payment for order #'.$this->order->id.' has been received.')
            ->line('Total charged: $'.number_format((float) $this->order->total, 2))
            ->line('Vendor: '.$this->order->vendor->name)
            ->action('View order', url('/orders/'.$this->order->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status->value,
            'total' => $this->order->total,
            'vendor' => $this->order->vendor->name,
            'message' => 'Payment confirmed for order #'.$this->order->id,
        ];
    }
}
