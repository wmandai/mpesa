<?php

namespace Wmandai\Mpesa\Notifications;

use Illuminate\Bus\Queueable;
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
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $message = '```' . json_encode($this->payload['message'], JSON_PRETTY_PRINT) . '```';

        return (new SlackMessage())
            ->from('MPESA', ':mailbox_with_mail:')
            ->content('MPESA Payment Response')
            ->attachment(
                function ($attachment) use ($message) {
                    $attachment->title($this->payload['title'], url('/'))
                        ->fields(
                            [
                                'Message' => $message,
                            ]
                        );
                }
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
