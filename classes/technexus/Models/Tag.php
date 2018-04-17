<?php
namespace technexus\Models;

class Tag extends \Divergence\Models\Model
{
    //use \Divergence\Models\Versioning;
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];


    // ActiveRecord configuration
    public static $tableName = 'tags';
    public static $singularNoun = 'tag';
    public static $pluralNoun = 'tags';
    
    // versioning
    //static public $historyTable = 'test_history';
    //static public $createRevisionOnDestroy = true;
    //static public $createRevisionOnSave = true;
    
    public static $fields = [
        'Tag',
        'Slug',
        'ContextClass',
        'ContextID' => [
            'type' => 'integer',
            'unsigned' => true,
        ],
    ];
    
    public static $relationships = [
        'Creator' => [
            'type' => 'one-one'
            ,'class' => 'User'
            ,'local'	=>	'CreatorID'
            ,'foreign' => 'ID',
            //,'conditions' => 'Status != "Deleted"'
            //,'order' => ['name' => 'ASC']
        ],
        'BlogPosts' => [
            'type' => 'context-parent',
            'allowedClasses' => [
                '\technexus\\Models\\BlogPost',
            ],
        ],
    ];
}
