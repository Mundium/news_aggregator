<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordToken extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $link = url( "/reset/" . $this->token );

        return (new MailMessage)

            ->subject( 'Reset password' )
            ->line( "Hello ," )
            ->line( "You are receiving this email because we received a password reset request for your account." )
            ->action( 'Reset Password', $link )
            ->line( 'This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->line("\r\n\r\n Regards,  \r\n Innoscripta.");
    }
}
