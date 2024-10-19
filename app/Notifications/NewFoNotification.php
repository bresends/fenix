<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Fo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFoNotification extends Notification implements ShouldQueue
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
        return ['mail', WhatsAppChannel::class];
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
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        return [
            'message' => "*Notificação de FO*

Olá {$notifiable->name}!

Você está recebendo esta mensagem porque recebeu um FO.

*FO n°:* {$this->fo->id}
*Tipo:* {$this->fo->type->value}
*Data:* {$this->fo->created_at->format('d/m/Y H:i')}
*Motivo:* {$this->fo->reason}

Para visualizar o FO, acesse o link:
" . url("/app/fos/{$this->fo->id}/edit") . "

*Atenção:* Conforme o Art. 61 da NE-03, você tem até 1 hora antes do término do expediente do dia útil seguinte ao recebimento deste FO para apresentar justificativa ou dar ciência.

Atenciosamente,
CAEBM",
        ];
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
