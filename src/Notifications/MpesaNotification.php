<?php

namespace Wmandai\Mpesa\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class MpesaNotification extends Notification
{
    use Queueable;

    public $payload;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('MPESA response')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toSlack($notifiable)
    {
        $message = '```' . json_encode($this->payload['message'], JSON_PRETTY_PRINT) . '```';

        return (new SlackMessage)
            ->from('MPESA', ':mailbox_with_mail:')
            ->content('MPESA Payment Response')
            ->attachment(function ($attachment) use ($message) {
                $attachment->title($this->payload['title'], url('/'))
                    ->fields([
                        'Message' => $message,
                    ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
