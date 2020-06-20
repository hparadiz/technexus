<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus\Controllers\Records\Permissions;

use \technexus\App as App;

use \Divergence\Models\ActiveRecord as ActiveRecord;

trait AdminWriteGuestRead
{
    public function is()
    {
        return true;
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
        return App::$App->is_loggedin();
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
