<?php
namespace technexus\Controllers;

use \technexus\App as App;

use Divergence\IO\Database\MySQL as DB;
use \technexus\Controllers\Records\Tag as Tag;
use \technexus\Controllers\Records\BlogPost as BlogPost;

/**
 * Routes /api/
*/
class API extends \Divergence\Controllers\RequestHandler
{
    
    /**
     * Routes
     *  /api/blogpost
     *  /api/tags
     */
    public static function handleRequest()
    {
        switch ($action = $action ? $action : static::shiftPath()) {
            case 'blogpost':
                return BlogPost::handleRequest();

            case 'tags':
                return Tag::handleRequest();
            
        }
    }
}
