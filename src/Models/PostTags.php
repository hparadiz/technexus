<?php
namespace technexus\Models;

use technexus\Models\Tag;
use technexus\Models\BlogPost;
use Divergence\Models\Mapping\Column;
use Divergence\Models\Mapping\Relation;

class PostTags extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];

    public static $tableName = 'posttags';
    public static $singularNoun = 'posttag';
    public static $pluralNoun = 'posttages';
    
    #[Column(unsigned:true)]
    protected int $BlogPostID;

    #[Column(unsigned:true)]
    protected int $TagID;
    
    #[Relation(
        type: 'one-one',
        class: Tag::class,
        local: 'TagID'
    )]
    protected $Tag;

    #[Relation(
        type: 'one-one',
        class: BlogPost::class,
        local: 'BlogPostID'
    )]
    protected $BlogPost;
}
