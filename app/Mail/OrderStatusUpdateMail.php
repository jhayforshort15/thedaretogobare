<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Update on your order {$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        $messages = [
            'paid' => "Payment received — your order is confirmed and being prepared. We'll let you know when it ships.",
            'shipped' => "Great news — your order is on its way! 🥊",
            'completed' => 'Your order is complete. Thanks for repping D2GB!',
            'cancelled' => 'Your order has been cancelled. If this is unexpected, just reply to this email.',
            'pending' => 'Your order status has been updated.',
        ];

        return new Content(
            markdown: 'emails.orders.status-update',
            with: [
                'order' => $this->order,
                'headline' => match ($this->order->status) {
                    'paid' => 'Order Confirmed',
                    'shipped' => 'Your Order Has Shipped',
                    'completed' => 'Order Complete',
                    'cancelled' => 'Order Cancelled',
                    default => 'Order Update',
                },
                'body' => $messages[$this->order->status] ?? $messages['pending'],
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
