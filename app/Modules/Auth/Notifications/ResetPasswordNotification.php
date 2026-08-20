<?php

namespace App\Modules\Auth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Send a customized password reset email notification.
 *
 * This notification generates a password reset link that redirects
 * users to the application's frontend password reset page.
 *
 * @package App\Modules\Auth\Notifications
 * @author Sanjiv Kumar Kushwaha
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the password reset email message.
     *
     * @param mixed $notifiable The notifiable entity receiving the notification.
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hello!')
            ->line('We received a request to reset your password.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}