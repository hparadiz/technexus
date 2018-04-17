<?php
namespace technexus\Controllers;

class Main extends \Divergence\Controllers\RequestHandler
{
    public static function handleRequest()
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
        

        
        return Blog::handleRequest();
    }
}
