<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;

final class LoginController
{
    public function index(Request $request, array $params = []): Response
    {
        return Response::html(
            View::render('auth/login', [
                'title' => 'Sign in',
            ], null)
        );
    }
}