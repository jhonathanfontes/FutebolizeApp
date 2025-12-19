<?php

namespace App\Controllers;

use App\Services\PushService;
use CodeIgniter\API\ResponseTrait;

class PushController extends BaseController
{
    use ResponseTrait;

    public function subscribe()
    {
        $subscription = $this->request->getJSON(true);

        // TODO: Save the subscription to the database for the logged-in user.
        // For now, we'll just log it to a file.
        log_message('info', 'New push subscription: ' . json_encode($subscription));

        return $this->respondCreated(['success' => true]);
    }

    public function send()
    {
        $pushService = new PushService();
        $subscriptionData = [
            // This is a placeholder. In a real application, you would retrieve
            // the subscription from the database for a specific user.
            'endpoint' => 'YOUR_SUBSCRIPTION_ENDPOINT',
            'keys' => [
                'p256dh' => 'YOUR_SUBSCRIPTION_P256DH',
                'auth' => 'YOUR_SUBSCRIPTION_AUTH',
            ],
        ];

        $payload = json_encode([
            'title' => 'Test Notification',
            'body' => 'This is a test push notification from the server.',
            'icon' => '/pwa/icons/icon-192.png',
        ]);

        $success = $pushService->sendNotification($subscriptionData, $payload);

        if ($success) {
            return $this->respond(['success' => true]);
        }

        return $this->failServerError('Failed to send push notification.');
    }
}
