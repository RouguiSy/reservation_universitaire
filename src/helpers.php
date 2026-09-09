<?php

declare(strict_types=1);

use App\Security\CsrfService;

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return (new CsrfService())->getToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
