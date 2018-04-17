<?php
namespace technexus\Controllers;

use Divergence\IO\Database\MySQL as DB;

use \technexus\Controllers\Records\BlogPost as BlogPost;
use \technexus\App as App;

class API extends \Divergence\Controllers\RequestHandler
{
	
	/*
	 * check if logged in and show login page if not
	 */
	
	public static function handleRequest()
	{	
		switch($action = $action?$action:static::shiftPath())
		{
			case 'blogpost':
				return BlogPost::handleRequest();
			
		}
	}	
}