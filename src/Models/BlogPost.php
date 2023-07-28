<?php
namespace technexus\Models;

use Divergence\Models\Mapping\Column;
use Divergence\Models\Mapping\Relation;

class BlogPost extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];

    public static $tableName = 'blog_posts';
    public static $singularNoun = 'blogpost';
    public static $pluralNoun = 'blogposts';
    
    protected string $Title;
    protected string $Permalink;
    protected string $MainContent;

    #[Column(type:"timestamp",notnull:false)]
    protected $Edited;

    protected string $Status;
    
    #[Relation(
        type: 'one-one',
        class: User::class,
        local: 'CreatorID',
        foreign: 'ID'
    )]
    protected $Creator;

    #[Relation(
        type: 'one-many',
        class: PostTags::class,
        local: 'ID',
        foreign: 'BlogPostID'
    )]
    protected $Tags;
    
    public function getTags()
    {
        $Values = [];
        if ($Tags = $this->getValue('Tags')) {
            foreach ($Tags as $Tag) {
                $Values[] = $Tag->Tag->Tag;
            }
            return implode(',', $Values);
        } 
        return '';
    }

    public function __get($field)
    {
        switch ($field) {
            case 'ShareImage':
                return $this->getShareImage();
            case 'InternalPermaLink':
                return $this->getInternalPermaLink();
            case 'ExternalPermaLink':
                    return $this->getExternalPermaLink(true);

            default:
                return parent::__get($field);
        }
    }

    public function getPermaLink($hostname=false)
    {
        return ($hostname?'https://'.$_SERVER['SERVER_NAME']:null) .
        '/'.date('Y', $this->__get('Created')) . '/' . date('m', $this->__get('Created')).'/'.$this->__get('Permalink').'/';
    }
    
    public function getInternalPermaLink()
    {
        return $this->getPermaLink();
    }

    public function getExternalPermaLink()
    {
        return $this->getPermaLink(true);
    }

    /**
     * Quick and dirty
     * TODO: Change admin to let you select this image
     *
     * @return string|false
     */
    public function getShareImage()
    {
        preg_match("/\/media\/([0-9]*)/", $this->__get('MainContent'), $images);
        
        if (ctype_digit($images[1])) {
            return 'https://'.$_SERVER['SERVER_NAME'].'/media/thumbnail/'.$images[1].'/500x500/cropped/';
        } else {
            return false;
        }
    }

    public function saveTags($tags)
    {
        $SeenTags = [];
        
        foreach ($tags as $tag) {
            if (!$Tag = Tag::getByField('Tag', $tag)) {
                $Tag = Tag::create([
                    'Tag' => $tag,
                    'Slug' => strtolower($tag),
                ], true);
            }
            
            $SeenTags[] = $Tag->ID;
            
            $PostTagData = [
                'BlogPostID' => $this->ID,
                'TagID'		 => $Tag->ID,
            ];
            
            if (!$PostTag = PostTags::getByWhere($PostTagData)) {
                $PostTag = PostTags::create($PostTagData, true);
            }
        }
        
        
        // if a tag was not submitted with the save input we can assume it was deleted
        $rmQuery = "DELETE FROM `" . PostTags::$tableName . "` WHERE `BlogPostID`='{$this->ID}' AND `TagID` NOT IN (" . implode(',', $SeenTags) . ")";
        \Divergence\IO\Database\MySQL::nonQuery($rmQuery);
    }
    
    public function clearTags()
    {
        $rmQuery = "DELETE FROM `" . PostTags::$tableName . "` WHERE `BlogPostID`='{$this->ID}'";
        \Divergence\IO\Database\MySQL::nonQuery($rmQuery);
    }
    
    public function save($deep = true)
    {
        if ($this->isDirty) {
            $this->Edited = time();
        }
        
        if (isset($_POST['Tags'])) {
            if (empty($_POST['Tags'])) {
                $this->clearTags();
            } else {
                $TagData = explode(',', $_POST['Tags']);
                if (!$TagData) {
                    $TagData = $_POST['Tags'];
                }
                $this->saveTags($TagData);
            }
        }
        
        return parent::save($deep);
    }
}
