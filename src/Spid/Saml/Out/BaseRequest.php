<?php

namespace SpidPHP\Spid\Saml\Out;

class BaseRequest
{

    private function generateID()
    {
        return '_' . random_bytes(16);
    }
}