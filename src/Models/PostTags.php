<?php
namespace technexus\Models;

use technexus\Models\Tag;
use technexus\Models\BlogPost;
use Divergence\Models\Mapping\Column;
use Divergence\Models\Mapping\Relation;

class PostTags extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;

    public static $tableName = 'posttags';
    
    #[Column(unsigned:true)]
    private int $BlogPostID;

    #[Column(unsigned:true)]
    private int $TagID;
    
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
