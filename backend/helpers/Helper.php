<?php

namespace helpers;

class Helper
{
    public static function isLogged(): bool
    {
        return isset($_SESSION['user_info']['logged']) && $_SESSION['user_info']['logged'];
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_info']['privileges']) && $_SESSION['user_info']['privileges'] === 2;
    }

    public static function isUser(): bool
    {
        return isset($_SESSION['user_info']['privileges']) && $_SESSION['user_info']['privileges'] === 1;
    }

    public static function isTrain(): bool
    {
        return isset($_SESSION['user_info']['privileges']) && $_SESSION['user_info']['privileges'] === 0;
    }

    public static function generateApiResponse($response, $status_code = 200, $error_message = null, $error_code = 0)
    {
        return [
            'status_code' => $status_code,
            'response' => $response,
            'error' => [
                'error_message' => $error_message,
                'error_code' => $error_code
            ]
        ];
    }

    public static function arrayRecursiveDiff($aArray1, $aArray2): array
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = self::arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }
        return $aReturn;
    }

}