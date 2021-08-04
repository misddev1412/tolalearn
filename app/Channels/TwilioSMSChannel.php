<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioSMSChannel
{
    /**
     * @param $message
     * @param $recipients
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwilioSMS($notifiable);

        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");

        $twilio = new Client($account_sid, $auth_token);


        $twilio->messages->create($message['to'],
            [
                'from' => $twilio_number,
                'body' => $message['content']
            ]
        );
    }
}
