<?php
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

/**
 * API access to only logged in Sessions.
 * Use this for admin.
 */
trait LoggedIn
{
    public static function is()
    {
        return App::is_loggedin();
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

    public static function checkUploadAccess()
    {
        return static::is();
    }
    
    public static function checkAPIAccess()
    {
        return static::is();
    }
}
