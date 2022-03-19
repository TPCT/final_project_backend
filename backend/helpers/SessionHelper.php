<?php

namespace helpers;

class SessionHelper
{
    public static function setSessionInfo($username, $mac_address, $privileges, $user_id): bool
    {
        $_SESSION['user_info'] = [
            'username' => $username,
            'mac_address' => $mac_address,
            'logged' => True,
            'privileges' => $privileges,
            'user_id' => $user_id
        ];
        return True;
    }
}