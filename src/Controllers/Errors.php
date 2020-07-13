<?php
namespace technexus\Controllers;

use technexus\Responders\TwigBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Errors extends \Divergence\Controllers\RequestHandler
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        return $this->handlePageNotFound($request);
    }
    
    public function handlePageNotFound(RequestInterface $request, $data=[])
    {
        header("HTTP/1.0 404 Not Found");
        $this->responseBuilder = TwigBuilder::class;

        return $this->respond('404.twig', $data);
    }
}
