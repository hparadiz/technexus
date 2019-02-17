<?php
namespace technexus\Controllers;

use Divergence\Controllers\MediaRequestHandler;

/**
 * Routes /media
 */
class Media extends MediaRequestHandler
{
    use Records\Permissions\AdminWriteGuestRead;
}
