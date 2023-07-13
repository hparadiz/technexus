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
     * @link https://technex.us/admin/
     * @link https://technex.us/admin/posts/
     * @link https://technex.us/admin/users/
     * @link https://technex.us/admin/media/
     * @link https://technex.us/admin/backups/
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

            case 'users':
                return $this->users();
            
            case 'media':
                return $this->media();

            case 'backups':
                return $this->backups();
        }
    }

    /**
     * Displays login
     * @link project://views/admin/login.tpl
     */
    public function login(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/login.twig'));
    }

    /**
     * Display admin home page
     * @link project://views/admin/home.tpl
     */
    public function home(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/home.twig', [
            'BlogPosts' => BlogPost::getAll(['order'=>'Created DESC']),
        ]));
    }
    
    /**
     * Routes /admin/posts/new to $this->newpost
     * Handles route /admin/posts/$id by displaying editor
     *
     * @link project://views/admin/posts/edit.tpl
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
                'TagTypeAhead' => \technexus\Models\Tag::getTypeahead()
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

    /**
     * Routes /admin/users/
     *
     * @link project://views/admin/users.tpl
     */
    public function users(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/users.twig', [
            'Users' => \technexus\Models\User::getAll(),
        ]));
    }

    /**
     * Routes /admin/media/
     *
     * @link project://views/admin/media.tpl
     */
    public function media(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/media.twig', [
            'Media' => \Divergence\Models\Media\Media::getAll(),
        ]));
    }

    /**
     * Routes /admin/backups/
     *
     * @link project://views/admin/backups.tpl
     */
    public function backups(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/backups.twig', [
            //'Users' => \technexus\Models\User::getAll(),
        ]));
    }
}
