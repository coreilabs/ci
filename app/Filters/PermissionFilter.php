<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
{
    helper('permission'); // 🔥 IMPORTANTE

    if (!session('user')) {
        return redirect()->to('/login');
    }

    if ($arguments) {
        foreach ($arguments as $perm) {
            if (!hasPermission($perm)) {
                return redirect()->to('/dashboard');
            }
        }
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}