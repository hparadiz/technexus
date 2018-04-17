<?php
namespace technexus\Controllers;

class Errors extends \Divergence\Controllers\RequestHandler
{
	static public $extensionMIMETypes = array(
		'js' => 'application/javascript'
		,'php' => 'application/php'
		,'html' => 'text/html'
		,'css' => 'text/css'
		,'apk' => 'application/vnd.android.package-archive'
		,'woff' => 'application/x-font-woff'
		,'ttf' => 'font/ttf'
		,'eot' => 'application/vnd.ms-fontobject'
	);

	static public function handleStaticFile()
	{
		global $mainDB;
		$File = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_URL'];
		if(file_exists($File))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$Type = finfo_file($finfo, $File);
			$pathinfo = pathinfo($File);
			
			$Type = static::$extensionMIMETypes[$pathinfo['extension']];

			if($Type == 'application/php') {
				spl_autoload_unregister('bootstrap_class_loader');
			
				if(!$_GET['dev'])
				{
					error_reporting(0);
				}
			
				include($File);
				exit;
			}
			else if($Type == NULL)
			{
				return static::handlePageNotFound();
			}
			else
			{
				header('Content-Type:'.$Type);
				header('Content-Length: ' . filesize($File));
				readfile($File); exit;	
			}
		}
	}

	public static function handleRequest($action='404')
	{
	
		//static::handleStaticFile();
		
		switch($action)
		{
			
			case '404':
				return static::handlePageNotFound();
			
		}
		
	}
	
	public static function handlePageNotFound()
	{
		header("HTTP/1.0 404 Not Found");
		
		
		static::respond('404.tpl',$data);	
	}
}