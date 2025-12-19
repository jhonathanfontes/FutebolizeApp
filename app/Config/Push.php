<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Push extends BaseConfig
{
    public string $publicKey = 'YOUR_PUBLIC_KEY';
    public string $privateKey = 'YOUR_PRIVATE_KEY';
    public string $subject = 'mailto:your-email@example.com';
}
