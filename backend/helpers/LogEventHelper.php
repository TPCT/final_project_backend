<?php

namespace helpers;

class LogEventHelper
{
    public static function loginEvent($username, $mac_address): string
    {
        $event = [
            'event_type' => 'info',
            'data' => ['logged' => 'train has been logged in to the system'],
            'train_name' => $username,
            'mac_address' => $mac_address
        ];

        $event_json = json_encode($event);
        $event_json = addslashes($event_json);
        return trim($event_json);
    }

    public static function NormalEvent($username, $mac_address, $data): string
    {
        $event = [
            'event_type' => 'info',
            'data' => $data,
            'train_name' => $username,
            'mac_address' => $mac_address
        ];

        $event_json = json_encode($event);
        $event_json = addslashes($event_json);
        return trim($event_json);
    }

    public static function EmergencyEvent($username, $mac_address, $data): string
    {
        $event = [
            'event_type' => 'emergency',
            'data' => $data,
            'train_name' => $username,
            'mac_address' => $mac_address
        ];

        $event_json = json_encode($event);
        $event_json = addslashes($event_json);
        return trim($event_json);
    }
}