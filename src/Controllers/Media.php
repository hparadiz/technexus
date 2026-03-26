<?php
namespace technexus\Controllers;

use Divergence\Responders\Response;
use Psr\Http\Message\ResponseInterface;
use Divergence\Controllers\MediaRequestHandler;
use Divergence\Responders\EmptyBuilder;

/**
 * Routes /media
 */
class Media extends MediaRequestHandler
{
    use Records\Permissions\AdminWriteGuestRead;

    public function throwNotFoundError(): ResponseInterface {
        $response = new Response(new EmptyBuilder('not found'));
        $response->withDefaults(404);
        return $response;
    }
}
