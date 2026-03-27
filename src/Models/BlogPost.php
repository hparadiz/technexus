<?php
namespace technexus\Models;

use Divergence\Models\Mapping\Column;
use Divergence\Models\Mapping\Relation;

class BlogPost extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;

    public static $tableName = 'blog_posts';
    
    private string $Title;
    private string $Permalink;
    private string $MainContent;

    #[Column(type:"timestamp",notnull:false)]
    private $Edited;

    private string $Status;
    
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
        if ($Tags = $this->Tags) {
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
        $created = $this->getValue('Created');
        $permalink = (string) $this->getValue('Permalink');
        $timestamp = is_numeric($created) ? (int) $created : strtotime((string) $created);

        return ($hostname?'https://'.$_SERVER['SERVER_NAME']:null) .
        '/'.date('Y', $timestamp) . '/' . date('m', $timestamp).'/'.$permalink.'/';
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
        
        if (!isset($images[1])) {
            return false;
        }
        
        if (ctype_digit($images[1])) {
            return 'https://'.$_SERVER['SERVER_NAME'].'/media/thumbnail/'.$images[1].'/500x500/cropped/';
        } else {
            return false;
        }
    }

    public function saveTags($tags)
    {
        $SeenTags = [];

        $tags = array_values(array_unique(array_filter(array_map(function ($tag) {
            return trim((string) $tag);
        }, is_array($tags) ? $tags : [$tags]))));

        if (empty($tags)) {
            $this->clearTags();
            return;
        }
        
        foreach ($tags as $tag) {
            if (!$Tag = Tag::getByField('Tag', $tag)) {
                $Tag = Tag::create([
                    'Tag' => $tag,
                    'Slug' => $this->slugify($tag),
                ], true);
            }
            
            $SeenTags[] = $Tag->getPrimaryKeyValue();
            
            $PostTagData = [
                'BlogPostID' => $this->getPrimaryKeyValue(),
                'TagID'		 => $Tag->getPrimaryKeyValue(),
            ];
            
            if (!$PostTag = PostTags::getByWhere($PostTagData)) {
                $PostTag = PostTags::create($PostTagData, true);
            }
        }
        
        
        // if a tag was not submitted with the save input we can assume it was deleted
        $rmQuery = "DELETE FROM `" . PostTags::$tableName . "` WHERE `BlogPostID`='{$this->getPrimaryKeyValue()}' AND `TagID` NOT IN (" . implode(',', $SeenTags) . ")";
        \Divergence\IO\Database\MySQL::nonQuery($rmQuery);
    }
    
    public function clearTags()
    {
        $rmQuery = "DELETE FROM `" . PostTags::$tableName . "` WHERE `BlogPostID`='{$this->getPrimaryKeyValue()}'";
        \Divergence\IO\Database\MySQL::nonQuery($rmQuery);
    }
    
    public function save($deep = true)
    {
        if ($this->isDirty) {
            $this->Edited = time();
        }

        $this->Permalink = $this->normalizePermalink($this->Permalink ?: $this->Title ?: 'untitled');
        
        parent::save($deep);

        if (array_key_exists('Tags', $_POST)) {
            if (empty($_POST['Tags'])) {
                $this->clearTags();
            } else {
                $TagData = explode(',', $_POST['Tags']);
                $this->saveTags($TagData);
            }
        }
    }

    protected function normalizePermalink(string $permalink): string
    {
        $permalink = strtolower(trim($permalink));
        $permalink = preg_replace("/['\"]+/", '', $permalink);
        $permalink = preg_replace('/[^a-z0-9]+/', '-', $permalink);
        $permalink = trim($permalink, '-');

        return $permalink ?: 'untitled';
    }

    protected function slugify(string $tag): string
    {
        $slug = strtolower(trim($tag));
        $slug = preg_replace("/['\"]+/", '', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug ?: strtolower($tag);
    }
}
