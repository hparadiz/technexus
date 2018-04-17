<?php
namespace technexus\Controllers\Records;

class BlogPost extends \Divergence\Controllers\RecordsRequestHandler {
	use Permissions\LoggedIn;
	
	static public $recordClass = 'technexus\\Models\\BlogPost';
}