<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus\Controllers;

use \technexus\App as App;

use Divergence\Responders\Response;
use Divergence\Responders\TwigBuilder;
use Psr\Http\Message\RequestInterface;
use Divergence\IO\Database\MySQL as DB;
use Psr\Http\Message\ResponseInterface;
use \technexus\Models\BlogPost as BlogPost;

/**
 * Main controller for the admin
 */
class Admin extends \Divergence\Controllers\RequestHandler
{
    /**
     * Routes
     * @link https://technexu.us/admin/
     * @link https://technexu.us/admin/posts/
     *
     * @return void
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        if (!App::$App->Session->CreatorID) {
            return $this->login();
        }
        
        switch ($action = $this->shiftPath()) {
            case '':
                return $this->home();
            
            
            case 'posts':
                return $this->posts();
        }
    }

    /**
     * Displays login
     * @link project://views/admin/login.tpl
     * @return void
     */
    public function login(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/login.twig'));
    }

    /**
     * Display admin home page
     * @link project://views/admin/home.tpl
     * @return void
     */
    public function home(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/home.twig', [
            'BlogPosts' => BlogPost::getAll(['order'=>'Created DESC']),
        ]));
    }
    
    /**
     * Routes /admin/posts/new to static::newpost
     * Handles route /admin/posts/$id by displaying editor
     *
     * @link project://views/admin/posts/edit.tpl
     * @return void
     */
    public function posts(): ResponseInterface
    {
        switch ($action = $this->shiftPath()) {
            case 'new':
                return $this->newpost();
        }
        
        if ($BlogPost = BlogPost::getByID($action)) {
            return new Response(new TwigBuilder('admin/posts/edit.twig', [
                'BlogPost' => $BlogPost,
            ]));
        }
    }
    
    /**
     * Creates a new draft blog post and saves it to the database immediately.
     * Redirects you to /admin/posts/$id of the new blog post.
     *
     * @return void
     */
    public function newpost(): ResponseInterface
    {
        $BlogPost = BlogPost::create([
            'Title' => 'Untitled',
            'Permalink' => 'untitled',
            'Status' => 'Draft',
        ], true);
        
        header('Location: /admin/posts/'.$BlogPost->ID);
        exit;
    }
}
