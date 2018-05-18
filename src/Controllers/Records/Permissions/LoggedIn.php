<?php
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

trait LoggedIn
{
    public static function is()
    {
        return App::$Session->CreatorID ? true : false;
    }
    
    public static function checkBrowseAccess($arguments)
    {
        return static::is();
    }

    public static function checkReadAccess(ActiveRecord $Record)
    {
        return static::is();
    }
    
    public static function checkWriteAccess(ActiveRecord $Record)
    {
        return static::is();
    }
    
    public static function checkAPIAccess($responseID, $responseData, $responseMode)
    {
        return static::is();
    }
}
