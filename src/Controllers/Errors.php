<?php
namespace technexus\Controllers;

use technexus\Responders\TwigBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Errors extends \Divergence\Controllers\RequestHandler
{
    public function handle(RequestInterface $requestInterface): ResponseInterface
    {
        return $this->handlePageNotFound();
    }
    
    public function handlePageNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        
        return $this->respond(new TwigBuilder('404.twig'));
    }
}
