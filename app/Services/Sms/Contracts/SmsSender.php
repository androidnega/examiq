<?php

namespace App\Services\Sms\Contracts;

interface SmsSender
{
    public function send(string $phone, string $message): void;
}
