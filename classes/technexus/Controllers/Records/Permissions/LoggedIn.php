<?php
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

trait LoggedIn {
	
	public static function is() {
		return App::$Session->CreatorID?true:false;
	}
	
	static public function checkBrowseAccess($arguments)
	{
		return static::is();
	}

	static public function checkReadAccess(ActiveRecord $Record)
	{
		return static::is();
	}
	
	static public function checkWriteAccess(ActiveRecord $Record)
	{
		return static::is();
	}
	
	static public function checkAPIAccess($responseID, $responseData, $responseMode)
	{
		return static::is();
	}
}