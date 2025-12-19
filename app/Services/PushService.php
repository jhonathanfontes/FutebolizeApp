<?php

namespace App\Services;

use Config\Push as PushConfig;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushService
{
    private WebPush $webPush;
    private PushConfig $config;

    public function __construct()
    {
        $this->config = new PushConfig();
        $auth = [
            'VAPID' => [
                'subject' => $this->config->subject,
                'publicKey' => $this->config->publicKey,
                'privateKey' => $this->config->privateKey,
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    public function sendNotification(array $subscriptionData, string $payload): bool
    {
        $subscription = Subscription::create($subscriptionData);
        $report = $this->webPush->sendOneNotification($subscription, $payload, ['TTL' => 5000]);

        return $report->isSuccess();
    }
}
