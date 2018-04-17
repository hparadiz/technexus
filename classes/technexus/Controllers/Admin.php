<?php
namespace technexus\Controllers;

use \technexus\App as App;

use Divergence\IO\Database\MySQL as DB;
use \technexus\Models\BlogPost as BlogPost;

class Admin extends \Divergence\Controllers\RequestHandler
{
    
    /*
     * check if logged in and show login page if not
     */
    
    public static function login()
    {
        static::respond('admin/login.tpl');
    }
    
    public static function home()
    {
        static::respond('admin/home.tpl', [
            'BlogPosts' => BlogPost::getAll(['order'=>'Created DESC']),
        ]);
    }
    
    public static function posts()
    {
        if ($BlogPost = BlogPost::getByID(static::shiftPath())) {
            static::respond('admin/posts/edit.tpl', [
                'BlogPost' => $BlogPost,
            ]);
        }
    }
    
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
}
