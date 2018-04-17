<?php
namespace technexus\Models;

class Tag extends \Divergence\Models\Model {
	//use \Divergence\Models\Versioning;
	use \Divergence\Models\Relations;
	
	// support subclassing
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = [__CLASS__];


	// ActiveRecord configuration
	static public $tableName = 'tags';
	static public $singularNoun = 'tag';
	static public $pluralNoun = 'tags';
	
	// versioning
	//static public $historyTable = 'test_history';
	//static public $createRevisionOnDestroy = true;
	//static public $createRevisionOnSave = true;
	
	static public $fields = [
        'Tag',
        'Slug',
		'ContextClass',
		'ContextID' => [
			'type' => 'integer',
			'unsigned' => true
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
	    'BlogPosts' => [
		    'type' => 'context-parent',
		    'allowedClasses' => [
				'\technexus\\Models\\BlogPost'
			]    
	    ]
	];
}