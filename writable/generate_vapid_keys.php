<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$vapidKeys = VAPID::createVapidKeys();

echo json_encode($vapidKeys);
