<?php
namespace technexus\Models;

class Tag extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];

    public static $tableName = 'tags';
    public static $singularNoun = 'tag';
    public static $pluralNoun = 'tags';
    
    protected $Tag;
    protected $Slug;

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
