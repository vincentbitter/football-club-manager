<?php

if (! defined('ABSPATH')) {
    exit;
}


interface CaptchaProvider
{
    public static function name(): string;
    public static function available(): bool;
    public function render(): string;
    public function validate($request): bool;
}
