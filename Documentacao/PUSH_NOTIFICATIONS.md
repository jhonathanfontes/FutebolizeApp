# Push Notifications

This document explains how to set up and use the push notification feature in this project.

## 1. Installation

This feature requires the `web-push/web-push` PHP library. Due to issues with the Composer environment, this library could not be installed automatically. You will need to install it manually:

```bash
composer require web-push/web-push
```

If you encounter issues, please check your Composer configuration and connection to Packagist.

## 2. VAPID Key Generation

Web push notifications use VAPID (Voluntary Application Server Identification) keys to secure the communication between your server and the push service. You need to generate a public and a private key.

**If you have `openssl` installed:**

You can generate the keys using the following commands:

```bash
# Generate a private key
openssl ecparam -name prime256v1 -genkey -noout -out private_key.pem

# Derive the public key from the private key
openssl ec -in private_key.pem -pubout -out public_key.pem
```

**If you don't have `openssl` installed:**

You can use an online VAPID key generator. Make sure to use a trusted service.

## 3. Configuration

Once you have your VAPID keys, you need to add them to the `app/Config/Push.php` file:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Push extends BaseConfig
{
    public string $publicKey = 'YOUR_PUBLIC_KEY';
    public string $privateKey = 'YOUR_PRIVATE_KEY';
    public string $subject = 'mailto:your-email@example.com';
}
```

Replace `YOUR_PUBLIC_KEY` and `YOUR_PRIVATE_KEY` with the keys you generated. The `subject` should be a `mailto` link for your administrative contact.

## 4. How It Works

### Frontend

-   The user is prompted to subscribe to push notifications when they click the "Subscribe to Notifications" button on the home page.
-   The `home.js.twig` file contains the JavaScript logic for subscribing the user and sending the subscription details to the server.
-   The `sw.js` (service worker) handles incoming push events and displays the notifications to the user.

### Backend

-   The `PushController` (`app/Controllers/PushController.php`) has two main methods:
    -   `subscribe()`: Receives the push subscription from the client and is intended to save it to the database for the current user. (Currently, it only logs the subscription).
    -   `send()`: A test endpoint to send a push notification. In a real application, you would trigger this from your application's logic (e.g., when a new message arrives).
-   The `PushService` (`app/Services/PushService.php`) encapsulates the logic for sending the push notifications using the `web-push/web-push` library.

## 5. Sending Notifications

To send a notification, you can use the `PushService`. Here's an example:

```php
use App\Services\PushService;

$pushService = new PushService();
$subscriptionData = [
    // Retrieve this from the database for the target user
    'endpoint' => '...',
    'keys' => [
        'p256dh' => '...',
        'auth' => '...',
    ],
];

$payload = json_encode([
    'title' => 'New Message',
    'body' => 'You have a new message from John Doe.',
    'icon' => '/pwa/icons/icon-192.png',
]);

$pushService->sendNotification($subscriptionData, $payload);
```
