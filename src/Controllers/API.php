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

use Psr\Http\Message\RequestInterface;
use Divergence\IO\Database\MySQL as DB;
use Psr\Http\Message\ResponseInterface;
use \technexus\Controllers\Records\Tag as Tag;
use \technexus\Controllers\Records\BlogPost as BlogPost;
use \technexus\Controllers\Media as Media;

/**
 * Routes /api/
*/
class API extends \Divergence\Controllers\RequestHandler
{
    
    /**
     * Routes
     *  /api/blogpost
     *  /api/media
     *  /api/tags
     */
    public function handle(RequestInterface $request):ResponseInterface
    {
        switch ($action = $this->shiftPath()) {
            case 'blogpost':
                return (new BlogPost())->handle($request);

            case 'media':
                return (new Media())->handle($request);

            case 'tags':
                return (new Tag())->handle($request);
            
        }
    }
}
