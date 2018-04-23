<?php
namespace technexus\Models;

class PostTags extends \Divergence\Models\Model
{
    //use \Divergence\Models\Versioning;
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];


    // ActiveRecord configuration
    public static $tableName = 'posttags';
    public static $singularNoun = 'posttag';
    public static $pluralNoun = 'posttages';
    
    // versioning
    //static public $historyTable = 'test_history';
    //static public $createRevisionOnDestroy = true;
    //static public $createRevisionOnSave = true;
    
    public static $fields = [
        'BlogPostID' => [
            'type' => 'integer'
            ,'unsigned' => true,
        ],
        'TagID' => [
            'type' => 'integer'
            ,'unsigned' => true,
        ],
    ];
    
    public static $relationships = [
        'Tag' => [
            'type' => 'one-one',
            'class' => '\technexus\Models\Tag',
            'local' => 'TagID',
        ],
        'BlogPost' => [
            'type' => 'one-one',
            'class' => '\technexus\Models\BlogPost',
            'local' => 'BlogPostID',
        ],
    ];
}
