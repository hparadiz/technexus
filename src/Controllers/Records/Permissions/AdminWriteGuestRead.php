<?php
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

trait AdminWriteGuestRead
{
    public static function is()
    {
        return true;
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
        return App::is_loggedin();
    }

    public static function checkUploadAccess()
    {
        return static::is();
    }
    
    public static function checkAPIAccess()
    {
        return static::is();
    }
}
