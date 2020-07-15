<?php
namespace technexus\Controllers;

use technexus\Models\BlogPost;
use Divergence\Responders\Response;
use technexus\Responders\RssBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Divergence\Controllers\RequestHandler;

class RSS extends RequestHandler
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        $BlogPosts = BlogPost::getAll([
            'order' =>  'Created DESC',
            'limit' => 10,
        ]);
        return new Response(new RssBuilder('blog/rss.twig', [
            'BlogPosts' => $BlogPosts,
            'Hostname' => $_SERVER['SERVER_NAME'],
        ]));
    }
}
