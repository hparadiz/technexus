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
    public function is()
    {
        return App::$App->is_loggedin();
    }
    
    public function checkBrowseAccess($arguments)
    {
        return $this->is();
    }

    public function checkReadAccess(ActiveRecord $Record)
    {
        return $this->is();
    }
    
    public function checkWriteAccess(ActiveRecord $Record)
    {
        return $this->is();
    }

    public function checkUploadAccess()
    {
        return $this->is();
    }
    
    public function checkAPIAccess()
    {
        return $this->is();
    }
}
