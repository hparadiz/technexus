<?php
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

trait LoggedInMedia
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
