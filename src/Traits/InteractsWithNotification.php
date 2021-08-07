<?php

namespace Wmandai\Mpesa\Traits;

use Illuminate\Support\Facades\Notification;
use Wmandai\Mpesa\Notifications\MpesaNotification;

trait InteractsWithNotification
{
    public function notify($title, $important = false)
    {
        $slack = config('mpesa.notifications.slack.webhook');
        if (!$important && empty($slack) && config('mpesa.notifications.only_important')) {
            return;
        }
        $payload = [
            'message' => request()->all(),
            'title' => $title,
        ];

        Notification::route('slack', config('mpesa.notifications.slack.webhook'))
            ->notify(new MpesaNotification($payload));
    }
}
