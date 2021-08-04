<?php

namespace App\Notifications;

use App\Channels\TwilioSMSChannel;
use Illuminate\Notifications\Notification;

class SendVerificationSMSCode extends Notification
{
    private $notifiable;

    /**
     * Create a new notification instance.
     *
     * @param $notifiable
     */
    public function __construct($notifiable)
    {
        $this->notifiable = $notifiable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioSMSChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toTwilioSMS($notifiable)
    {
        return [
            'to' => $notifiable->mobile,
            'content' => $notifiable->code,
        ];
    }
}
