<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('user')) {
            return redirect()->to('/login');
        }

        if ($arguments) {
            $userRole = session()->get('user')['role'];

            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}