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
    ];

    public static $validators = [
        [
            'field' => 'Tag',
            'minlength' => 2,
            'errorMessage' => 'Tag must be at least two characters.',
        ]
    ];
    
    public static function getTypeahead()
    {
        $Tags = static::getAll();
        $Values = [];
        foreach ($Tags as $Tag) {
            $Values[] = $Tag->Tag;
        }
        return "'".implode("','", $Values)."'";
    }
}
