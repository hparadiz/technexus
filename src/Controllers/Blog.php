<?php
namespace technexus\Controllers;

use \technexus\App as App;

use Divergence\IO\Database\MySQL as DB;
use \technexus\Models\BlogPost as BlogPost;
use \technexus\Models\PostTags as PostTags;

/**
 * Main Blog controller
 */
class Blog extends \Divergence\Controllers\RequestHandler
{
    /**
     * Handles main routing
     * @return mixed
     */
    public static function handleRequest()
    {
        switch ($action = $action ? $action : static::shiftPath()) {
            case 'admin':
                return Admin::handleRequest();
                
            case 'api':
                return API::handleRequest();

            case 'media':
                return Media::handleRequest();
                
            case 'logout':
                return static::logout();
                    
            case '':
                return static::home();
                break;

            case 'topics':
                return static::topics();

            case ctype_digit($action):
                // year of posts
                if (strlen($action) == 4) {
                    $year = $action;
                }
                // month of posts
                if (ctype_digit(static::peekPath()) && strlen(static::peekPath()) == 2) {
                    $month = static::shiftPath();
                }
                // single post
                if (static::peekPath()) {
                    $permalink = static::shiftPath();
                }
                
                if (!$permalink && !$month) {
                    return static::year($year);
                }
                
                if (!$permalink) {
                    return static::month($year, $month);
                }
                
                return static::post($year, $month, $permalink);
                
            default:

                
                // code for tag(s)
                // [tag+[tag]]
                break;
        }
    }

    /**
     * Gets stuff that goes into the sidebar. All Months with blog posts and tags ordered by the amount of times that tag was used.
     *
     * @link project://views/blog/sidebar.tpl
     * @return array
     */
    public static function getSidebarData()
    {
        return [
            'Months' => DB::AllRecords(sprintf('SELECT DISTINCT MONTHNAME(`Created`) as `MonthName`,MONTH(`Created`) as `Month`, YEAR(`Created`) as `Year` FROM `%s` ORDER BY `Created` DESC', BlogPost::$tableName)),
            'Tags' => PostTags::getAllbyQuery('SELECT *,COUNT(*) as `Count` FROM `'.PostTags::$tableName.'` GROUP BY `TagID` ORDER BY `Count` DESC'),
        ];
    }
    
    /**
     * Returns conditions for which blog posts to display
     *
     * @return void
     */
    public static function conditions()
    {
        if (App::is_loggedin()) {
            return [
                "`Status` IN ('Draft','Published')",
            ];
        } else {
            return [
                "`Status` IN ('Published')",
            ];
        }
    }

    /**
     * Displays main home page.
     *
     * @link project://views/blog/posts.tpl
     * @link project://views/templates/post.tpl
     * @return void
     */
    public static function home()
    {
        $BlogPosts = BlogPost::getAllByWhere(array_merge(static::conditions(), [
            
        ]), [
            'order' =>  'Created DESC',
        ]);
        
        return static::respond('blog/posts.tpl', [
            'BlogPosts' => $BlogPosts,
            'Sidebar' => static::getSidebarData(),
        ]);
    }
    
    /**
     * Displays a given year of blog posts
     *
     * @param string $year
     * @link project://views/blog/posts.tpl
     * @link project://views/templates/post.tpl
     * @return void
     */
    public static function year($year)
    {
        $BlogPosts = BlogPost::getAllByWhere(array_merge(static::conditions(), [
            sprintf('YEAR(`Created`)=%d', $year),
        ]), [
            'order' =>  'Created DESC',
        ]);
        
        return static::respond('blog/posts.tpl', [
            'BlogPosts' => $BlogPosts,
            'Sidebar' => static::getSidebarData(),
        ]);
    }
    
    /**
     * Displays a month of blogs
     *
     * @param string $year
     * @param string $month
     * @link project://views/blog/posts.tpl
     * @link project://views/templates/post.tpl
     * @return void
     */
    public static function month($year, $month)
    {
        $BlogPosts = BlogPost::getAllByWhere(array_merge(static::conditions(), [
            sprintf('YEAR(`Created`)=%d', $year),
            sprintf('MONTH(`Created`)=%d', $month),
        ]), [
            'order' =>  'Created DESC',
        ]);
        
        return static::respond('blog/posts.tpl', [
            'BlogPosts' => $BlogPosts,
            'Sidebar' => static::getSidebarData(),
        ]);
    }
    
    /**
     * Displays a blog post
     *
     * @param string $year
     * @param string $month
     * @param string $permalink
     * @link project://views/blog/post.tpl
     * @link project://views/templates/post.tpl
     * @return void
     */
    public static function post($year, $month, $permalink)
    {
        $BlogPost = BlogPost::getByWhere(array_merge(static::conditions(), [
            sprintf('YEAR(`Created`)=%d', $year),
            sprintf('MONTH(`Created`)=%d', $month),
            "`permalink`='".DB::escape($permalink)."'",
        ]));
        
        return static::respond('blog/post.tpl', [
            'BlogPost' => $BlogPost,
            'Sidebar' => static::getSidebarData(),
        ]);
    }
    
    /**
     * Displays blog posts by tag.
     *
     * @link project://views/blog/post.tpl
     * @link project://views/templates/post.tpl
     * @return void
     */
    public static function topics()
    {
        if (static::peekPath()) {
            if (App::is_loggedin()) {
                $where = "`Status` IN ('Draft','Published')";
            } else {
                $where = "`Status` IN ('Published')";
            }
            if ($Tag = \technexus\Models\Tag::getByField('Slug', urldecode(static::shiftPath()))) {
                $BlogPosts = BlogPost::getAllByQuery(
                    "SELECT `bp`.* FROM `%s` `bp`
					INNER JOIN %s as `t` ON `t`.`BlogPostID`=`bp`.`ID`
					WHERE `t`.`TagID`='%s' AND $where",
                    [
                        BlogPost::$tableName,
                        PostTags::$tableName,
                        $Tag->ID,
                    ]
                );
                    
                return static::respond('blog/posts.tpl', [
                    'Title' => $Tag->Tag,
                    'BlogPosts' => $BlogPosts,
                    'Sidebar' => static::getSidebarData(),
                ]);
            }
        }
    }
    
    /**
     * Removes the user from the session and redirects to the homepage.
     *
     * @return void
     */
    public static function logout()
    {
        if (App::$Session->CreatorID) {
            App::$Session->CreatorID = null;
            App::$Session->save();
        }
        header('Location: /');
        exit;
    }
}
