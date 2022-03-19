<?php

namespace middlewares;

use core\MiddleWare;
use helpers\SecurityHelper;

class EncryptionLayerMiddleWare extends MiddleWare{
    private SecurityHelper $security_helper;
    public function __construct() {
        $this->setMiddleWareType(self::MIDDLEWARE_IS_MUST);
        $this->security_helper = new SecurityHelper();
        $this->security_helper->decryptSessionData();
    }

    public function Rules(): array
    {
        return [
            'security_checker' => self::MIDDLEWARE_IS_MUST
        ];
    }

    public function security_checker():bool{
        return True;
    }
}
