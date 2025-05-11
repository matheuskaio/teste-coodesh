<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SyncErrorNotification extends Notification
{
    use Queueable;

    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Falha durante a sincronização do Open Food Facts')
            ->line('Ocorreu um erro durante a importação de produtos.')
            ->line('Detalhes do erro:')
            ->line($this->message)
            ->line('Verifique o log ou consulte o administrador do sistema.');
    }
}
