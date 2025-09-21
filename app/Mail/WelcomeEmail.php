<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $password;
    public $shopName;
    public $realName; // Thêm thuộc tính mới

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $password, $shopName, $realName)
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->shopName = $shopName;
        $this->realName = $realName; // Gán giá trị
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chào mừng bạn đến với cửa hàng của chúng tôi!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'userName' => $this->userName,
                'password' => $this->password,
                'shopName' => $this->shopName,
                'realName' => $this->realName, // Truyền tên thật vào view
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
