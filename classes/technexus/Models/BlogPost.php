<?php
namespace technexus\Models;

class BlogPost extends \Divergence\Models\Model {
	//use \Divergence\Models\Versioning;
	use \Divergence\Models\Relations;
	
	// support subclassing
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = [__CLASS__];


	// ActiveRecord configuration
	static public $tableName = 'blog_posts';
	static public $singularNoun = 'blogpost';
	static public $pluralNoun = 'blogposts';
	
	// versioning
	//static public $historyTable = 'test_history';
	//static public $createRevisionOnDestroy = true;
	//static public $createRevisionOnSave = true;
	
	static public $fields = [
        'Title',
        'Permalink',
        'MainContent',
        'Edited' => [
	        'type' => 'timestamp',
	        'notnull' => false
        ]
        
	];
	
	static public $relationships = [
		'Creator' => [
	    	'type' => 'one-one'
	    	,'class' => 'User'
	    	,'local'	=>	'CreatorID'
	    	,'foreign' => 'ID'
	    	//,'conditions' => 'Status != "Deleted"'
	    	//,'order' => ['name' => 'ASC']
	    ],
	    'Tags' => [
		    'type' => 'context-children',
		    'class' => '\technexus\Models\Tag',
		    'local' => 'ID'
	    ]
	];
}