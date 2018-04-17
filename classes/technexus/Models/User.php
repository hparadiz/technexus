<?php
namespace technexus\Models;

class User extends \Divergence\Models\Model {
	//use \Divergence\Models\Versioning;
	use \Divergence\Models\Relations;
	
	// support subclassing
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = [__CLASS__];


	// ActiveRecord configuration
	static public $tableName = 'users';
	static public $singularNoun = 'user';
	static public $pluralNoun = 'users';
	
	// versioning
	//static public $historyTable = 'test_history';
	//static public $createRevisionOnDestroy = true;
	//static public $createRevisionOnSave = true;
	
	static public $fields = [
        'Email',
        'DisplayName',
        'PasswordHash'
	];
	
	static public $relationships = [
		/*'Creator' => [
	    	'type' => 'one-one'
	    	,'class' => 'User'
	    	,'local'	=>	'CreatorID'
	    	,'foreign' => 'ID'
	    	//,'conditions' => 'Status != "Deleted"'
	    	//,'order' => ['name' => 'ASC']
	    ]*/
	];
}