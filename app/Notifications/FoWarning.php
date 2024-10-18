<?php

namespace App\Notifications;

use App\Models\Fo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FoWarning extends Notification
{
    use Queueable;

    protected Fo $fo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Fo $fo)
    {
        $this->fo = $fo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/app/fos/{$this->fo->id}/edit");

        return (new MailMessage)
            ->subject('Notificacão de FO')
            ->markdown('mail.fo.created', ['url' => $url, 'fo' => $this->fo]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
