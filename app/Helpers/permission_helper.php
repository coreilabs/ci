<?php

if (!function_exists('hasPermission')) {

    function hasPermission($permission)
    {
        $user = session()->get('user');

        if (!$user || !isset($user['permissions']) || !is_array($user['permissions'])) {
            return false;
        }

        return in_array($permission, $user['permissions'], true);
    }
}