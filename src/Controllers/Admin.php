<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus\Controllers;

use Divergence\Helpers\Util;
use Ifsnop\Mysqldump;
use \technexus\App as App;
use Divergence\IO\Database\MySQL;
use Divergence\Responders\Response;
use Divergence\Responders\EmptyBuilder;
use Divergence\Responders\TwigBuilder;
use Psr\Http\Message\RequestInterface;
use Divergence\Responders\MediaBuilder;
use Psr\Http\Message\ResponseInterface;
use Divergence\Responders\MediaResponse;
use \technexus\Models\BlogPost as BlogPost;
use technexus\Models\User;

/**
 * Main controller for the admin
 */
class Admin extends \Divergence\Controllers\RequestHandler
{
    /**
     * Routes
     * @link https://technex.us/admin/
     * @link https://technex.us/admin/posts/
     * @link https://technex.us/admin/users/
     * @link https://technex.us/admin/media/
     * @link https://technex.us/admin/backups/
     *
     * @return void
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        if (!App::$App->Session->CreatorID) {
            return $this->login();
        }
        
        switch ($action = $this->shiftPath()) {
            case '':
                return $this->home();
            
            case 'posts':
                return $this->posts();

            case 'users':
                return $this->users();
            
            case 'media':
                return $this->media();

            case 'backups':
                return $this->backups();
        }
    }

    /**
     * Displays login
     * @link project://views/admin/login.tpl
     */
    public function login(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/login.twig'));
    }

    /**
     * Display admin home page
     * @link project://views/admin/home.tpl
     */
    public function home(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/home.twig', [
            'BlogPosts' => BlogPost::getAll(['order'=>'Created DESC']),
        ]));
    }
    
    /**
     * Routes /admin/posts/new to $this->newpost
     * Handles route /admin/posts/$id by displaying editor
     *
     * @link project://views/admin/posts/edit.tpl
     */
    public function posts(): ResponseInterface
    {
        switch ($action = $this->shiftPath()) {
            case 'new':
                return $this->newpost();
        }
        
        if ($BlogPost = BlogPost::getByID($action)) {
            return new Response(new TwigBuilder('admin/posts/edit.twig', [
                'BlogPost' => $BlogPost,
                'TagsValue' => $BlogPost->getTags(),
                'InitialTags' => $BlogPost->getTagValues(),
                'TagTypeAhead' => \technexus\Models\Tag::getTypeahead()
            ]));
        }
    }
    
    /**
     * Creates a new draft blog post and saves it to the database immediately.
     * Redirects you to /admin/posts/$id of the new blog post.
     *
     * @return void
     */
    public function newpost(): ResponseInterface
    {
        $BlogPost = BlogPost::create([
            'Title' => 'Untitled',
            'Permalink' => 'untitled',
            'Status' => 'Draft',
        ], true);
        
        header('Location: /admin/posts/'.$BlogPost->ID);
        exit;
    }

    /**
     * Routes /admin/users/
     *
     * @link project://views/admin/users.tpl
     */
    public function users(): ResponseInterface
    {
        switch ($action = $this->shiftPath()) {
            case 'delete':
                return $this->deleteUser($this->shiftPath());
        }

        return new Response(new TwigBuilder('admin/users.twig', [
            'Users' => User::getAll(['order' => 'ID DESC']),
            'CurrentUserID' => App::$App->Session->CreatorID,
            'Notice' => $this->getNotice(),
        ]));
    }

    /**
     * Routes /admin/media/
     *
     * @link project://views/admin/media.tpl
     */
    public function media(): ResponseInterface
    {
        return new Response(new TwigBuilder('admin/media.twig', [
            'Notice' => $this->getNotice(),
        ]));
    }

    /**
     * Routes /admin/backups/
     *
     * @link project://views/admin/backups.tpl
     */
    public function backups(): ResponseInterface
    {
        switch ($action = $this->shiftPath()) {
            case '':
                return new Response(new TwigBuilder('admin/backups.twig', [
                    'configurations'
                ]));;
            
            case 'download':
                return $this->downloadBackup();
        }

    }

    public function downloadBackup(): ResponseInterface
    {
        try {
            $dbConfig = App::$App->config('db');
            $config = $dbConfig[MySQL::$currentConnection];
            
            $tmpName = tempnam('/tmp','db');

            $dump = new Mysqldump\Mysqldump('mysql:host='.$config['host'].';dbname='.$config['database'], $config['username'], $config['password'],[
                'compress' => Mysqldump\Mysqldump::BZIP2 
            ]);
            $dump->start($tmpName);

        } catch (\Exception $e) {
            throw $e;
        } 

        $this->responseBuilder = MediaBuilder::class;
        $className = $this->responseBuilder;
        $responseBuilder = new $className($tmpName, []);

        $responseBuilder->setContentType('application/x-bzip2');

        $response = new MediaResponse($responseBuilder);
        $filename = 'backup-'.$_SERVER['SERVER_NAME'].'-'.date('Y-m-d').'.sql.bz2';

        $response = $response->withHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $response = $response->withHeader('ETag', 'backup-'.$tmpName)
            ->withHeader('Content-Length', filesize($tmpName));

        return $response;
    }

    protected function deleteUser($userID): ResponseInterface
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/users');
        }

        if (!ctype_digit((string) $userID) || !$User = User::getByID($userID)) {
            return $this->redirect('/admin/users?notice=user-delete-missing');
        }

        if ((int) $User->ID === (int) App::$App->Session->CreatorID) {
            return $this->redirect('/admin/users?notice=user-delete-self');
        }

        $User->destroy();

        return $this->redirect('/admin/users?notice=user-deleted');
    }

    protected function getNotice(): ?array
    {
        return match ($_GET['notice'] ?? null) {
            'user-deleted' => ['type' => 'success', 'message' => 'User deleted.'],
            'user-delete-missing' => ['type' => 'danger', 'message' => 'User not found.'],
            'user-delete-self' => ['type' => 'warning', 'message' => 'Refusing to delete the account currently logged in.'],
            default => null,
        };
    }

    protected function redirect(string $url): ResponseInterface
    {
        return (new Response(new EmptyBuilder('')))->withStatus(302)->withHeader('Location', $url);
    }
}
