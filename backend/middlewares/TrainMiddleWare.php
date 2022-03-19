<?php

namespace middlewares;

use helpers\Helper;

class TrainMiddleWare extends \core\MiddleWare
{

    public function Rules(): array
    {
        return [
            'isTrain' => self::MIDDLEWARE_RULE_IS_MUST
        ];
    }

    public function isTrain(): bool{
        return Helper::isLogged() && Helper::isTrain();
    }
}