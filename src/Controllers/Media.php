<?php
namespace technexus\Controllers;

use Divergence\Controllers\MediaRequestHandler;

class Media extends MediaRequestHandler
{
    use Records\Permissions\LoggedInMedia;
}
