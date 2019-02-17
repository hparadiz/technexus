<?php
namespace technexus\Controllers;

use \technexus\App as App;

use Divergence\IO\Database\MySQL as DB;
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
    public static function handleRequest()
    {
        if (!App::$Session->CreatorID) {
            return static::login();
        }
        
        switch ($action = $action ? $action : static::shiftPath()) {
            case '':
                return static::home();
            
            
            case 'posts':
                return static::posts();
        }
    }

    /**
     * Displays login
     * @link project://views/admin/login.tpl
     * @return void
     */
    public static function login()
    {
        static::respond('admin/login.tpl');
    }

    /**
     * Display admin home page
     * @link project://views/admin/home.tpl
     * @return void
     */
    public static function home()
    {
        static::respond('admin/home.tpl', [
            'BlogPosts' => BlogPost::getAll(['order'=>'Created DESC']),
        ]);
    }
    
    /**
     * Routes /admin/posts/new to static::newpost
     * Handles route /admin/posts/$id by displaying editor
     *
     * @link project://views/admin/posts/edit.tpl
     * @return void
     */
    public static function posts()
    {
        switch ($action = $action ? $action : static::shiftPath()) {
            case 'new':
                return static::newpost();
        }
        
        if ($BlogPost = BlogPost::getByID($action)) {
            static::respond('admin/posts/edit.tpl', [
                'BlogPost' => $BlogPost,
            ]);
        }
    }
    
    /**
     * Creates a new draft blog post and saves it to the database immediately.
     * Redirects you to /admin/posts/$id of the new blog post.
     *
     * @return void
     */
    public static function newpost()
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
