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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This runs from
 * @link project://bootstrap/router.php
 */
class Main extends \Divergence\Controllers\RequestHandler
{
    /**
     * Turns over control immediately to Blog::handleRequest();
     *
     * Sets up down page.
     * @link project://site.LOCK exists in the project
     * Displays project://down.html if it does.
     *
     * @uses Blog::handleRequest()
     * @return void
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/site.LOCK')) {
            echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/down.html');
            exit;
        }

        /*
         * This is to make sure any page that loads
         * through Apache's ErrorDocument returns 200
         * instead of 404.
         */
        header('HTTP/1.0 200 OK');
        //header('X-Powered-By: PHP/' . phpversion() . ' Div Framework (http://emr.ge) Henry\'s Revision');

        $blog = new Blog();
        return $blog->handle($request);
    }
}
