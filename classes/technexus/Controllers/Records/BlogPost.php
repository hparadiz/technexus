<?php
namespace technexus\Controllers\Records;

class BlogPost extends \Divergence\Controllers\RecordsRequestHandler
{
    use Permissions\LoggedIn;
    
    public static $recordClass = 'technexus\\Models\\BlogPost';
}
