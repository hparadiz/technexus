<?php
namespace technexus\Models;

class BlogPost extends \Divergence\Models\Model
{
    //use \Divergence\Models\Versioning;
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];


    // ActiveRecord configuration
    public static $tableName = 'blog_posts';
    public static $singularNoun = 'blogpost';
    public static $pluralNoun = 'blogposts';
    
    // versioning
    //static public $historyTable = 'test_history';
    //static public $createRevisionOnDestroy = true;
    //static public $createRevisionOnSave = true;
    
    public static $fields = [
        'Title',
        'Permalink',
        'MainContent',
        'Edited' => [
            'type' => 'timestamp',
            'notnull' => false,
        ],
        'Status'
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
        'Tags' => [
            'type' => 'context-children',
            'class' => '\technexus\Models\Tag',
            'local' => 'ID',
        ],
    ];
    
    public function save($deep = true)
    {
	    if ($this->isDirty) {
		    $this->Edited = time();
		}
	    return parent::save($deep);
	}
}
