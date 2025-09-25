<?php

namespace App\Mail;

use App\Models\DonHang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public DonHang $order;
    public array $orderDetails;
    public string $orderUrl;
    public string $paymentUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(DonHang $order, array $orderDetails = [])
    {
        $this->order = $order;
        $this->orderDetails = $orderDetails;
        $this->orderUrl = route('orders.detail', $order->id);
        $this->paymentUrl = route('bank.payment.info', $order->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng #' . $this->order->id . ' - ThriftZone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmation',
            with: [
                'order' => $this->order,
                'orderDetails' => $this->orderDetails,
                'orderUrl' => $this->orderUrl,
                'paymentUrl' => $this->paymentUrl,
                'totalAmount' => number_format($this->order->tongtien, 0, ',', '.') . ' ₫',
                'orderDate' => $this->order->ngaytao->format('d/m/Y H:i'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
