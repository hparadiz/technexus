<?php
namespace technexus\Models;

class Tag extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;

    public static $tableName = 'tags';
    
    private $Tag;
    private $Slug;

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
