<?php
namespace technexus\Controllers;

use Divergence\Responders\Response;
use Psr\Http\Message\ResponseInterface;
use Divergence\Controllers\MediaRequestHandler;
use Divergence\Responders\EmptyBuilder;
use Divergence\Responders\JsonBuilder;
use Psr\Http\Message\ServerRequestInterface;
use technexus\App;

/**
 * Routes /media
 */
class Media extends MediaRequestHandler
{
    use Records\Permissions\AdminWriteGuestRead;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // The framework upload endpoint calls its access hook without checking
        // the return value, so enforce the write boundary before dispatch.
        if (!App::$App->is_loggedin() && !in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return (new Response(new JsonBuilder('Unauthorized', [
                'success' => false,
                'message' => 'Login required.',
            ])))->withStatus(403);
        }

        return parent::handle($request);
    }

    public function throwNotFoundError(): ResponseInterface {
        $response = new Response(new EmptyBuilder('not found'));
        $response->withDefaults(404);
        return $response;
    }
}
