<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrintifyIssueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $issue) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action needed: order {$this->order->order_number} has a Printify problem",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.printify-issue',
            with: [
                'order' => $this->order,
                'issue' => $this->issue,
                'adminUrl' => url("/admin/orders/{$this->order->id}/edit"),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
