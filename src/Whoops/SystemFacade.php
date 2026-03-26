<?php

namespace technexus\Whoops;

class SystemFacade extends \Whoops\Util\SystemFacade
{
    public function setHttpResponseCode($httpCode)
    {
        if (headers_sent()) {
            return http_response_code();
        }

        header_remove('location');

        return @http_response_code($httpCode);
    }
}
