<?php

namespace App\Mail;

use App\Models\UserModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public UserModel $user;
    public string $token;

    public function __construct(UserModel $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $verifyUrl = url('/verify-email/' . $this->token);
        return $this->subject('Xác minh email của bạn')
            ->view('emails.verify_email', [
                'user' => $this->user,
                'verifyUrl' => $verifyUrl,
            ]);
    }
}


