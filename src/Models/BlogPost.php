<?php
namespace technexus\Models;

class BlogPost extends \Divergence\Models\Model
{
    //use \Divergence\Models\Versioning;
    use \Divergence\Models\Relations;
    
    // support subclassing
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];


    // ActiveRecord configuration
    public static $tableName = 'blog_posts';
    public static $singularNoun = 'blogpost';
    public static $pluralNoun = 'blogposts';
    
    // versioning
    //static public $historyTable = 'test_history';
    //static public $createRevisionOnDestroy = true;
    //static public $createRevisionOnSave = true;
    
    public static $fields = [
        'Title',
        'Permalink',
        'MainContent',
        'Edited' => [
            'type' => 'timestamp',
            'notnull' => false,
        ],
        'Status',
    ];
    
    public static $relationships = [
        'Creator' => [
            'type' => 'one-one',
            'class' => 'User',
            'local'	=>	'CreatorID',
            'foreign' => 'ID',
            //,'conditions' => 'Status != "Deleted"'
            //,'order' => ['name' => 'ASC']
        ],
        'Tags' => [
            'type' => 'one-many',
            'class' => PostTags::class,
            'local' => 'ID',
            'foreign' => 'BlogPostID',
        ],
    ];
    
    public function getTags()
    {
        $Values = [];
        foreach ($this->Tags as $Tag) {
            $Values[] = $Tag->Tag->Tag;
        }
        return implode(',', $Values);
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
        '/'.date('Y', $this->Created) . '/' . date('m', $this->Created).'/'.$this->Permalink.'/';
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
        preg_match("/\/media\/([0-9]*)/", $this->MainContent, $images);
        
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
