<?php
namespace technexus\Models;

/**
 * User model
 * @inheritDoc
 */
class User extends \Divergence\Models\Model
{
    //use \Divergence\Models\Versioning;
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];


    // ActiveRecord configuration
    public static $tableName = 'users';
    public static $singularNoun = 'user';
    public static $pluralNoun = 'users';
    
    // versioning
    //static public $historyTable = 'test_history';
    //static public $createRevisionOnDestroy = true;
    //static public $createRevisionOnSave = true;
    
    protected string $Email;
    protected string $DisplayName;
    protected string $PasswordHash;
    
    public static $relationships = [
        /*'Creator' => [
            'type' => 'one-one',
            'class' => 'User',
            'local'	=>	'CreatorID',
            'foreign' => 'ID',
            //,'conditions' => 'Status != "Deleted"'
            //,'order' => ['name' => 'ASC']
        ]*/
    ];
}
