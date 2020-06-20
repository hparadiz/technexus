<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus;

use technexus\Models\User;
use technexus\Models\Session;
use technexus\Controllers\Main;
use Divergence\Responders\Emitter;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Bootstraps site and provides some global references.
 * @inheritDoc
 */
class App extends \Divergence\App
{
    /** @var Session */
    public Session $Session;

    public function __get($field)
    {
        switch ($field) {
            case 'LoadTime':
                return $this->getLoadTime();
        }
    }
    
    /**
     * Bootstraps the site.
     *
     * - Sets error handling.
     * - Sets local timezone
     * - Gets or sets Session and stores App::$Session
     * - Checks if login attempt and tries to login
     *
     * @param string $Path
     * @uses $this->Session
     * @return void
     * @inheritDoc
     */
    public function init($Path)
    {
        error_reporting(E_ALL & ~E_NOTICE);
        parent::init($Path);
        
        date_default_timezone_set('America/New_York');
        
        $tz = (new \DateTime('now', new \DateTimeZone('America/New_York')))->format('P');
        
        \Divergence\IO\Database\MySQL::nonQuery("SET time_zone='$tz';");
        $this->Session = Session::getFromRequest();
        $this->auth();
    }
    
    /**
     * Checks if login attempt and runs login code.
     *
     * @return void
     */
    public function auth()
    {
        if ($_POST['login']['email'] && $_POST['login']['password']) {
            $this->login($_POST['login']['email'], $_POST['login']['password']);
        }
    }

    /**
     * Checks login and sets session as logged in if login attempt successful.
     * If login successful redirects to same page url.
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login($username, $password)
    {
        if ($User = User::getByField('Email', $username)) {
            if (password_verify($password, $User->PasswordHash)) {
                $this->Session->CreatorID = $User->ID;
                $this->Session->save();
                header('Location: ' . $_SERVER['REDIRECT_URL']);
                exit;
            }
        }
    }
    
    /**
     * Checks if the current session is logged in
     *
     * @return boolean
     */
    public function is_loggedin()
    {
        if ($this->Session) {
            if ($this->Session->CreatorID) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Gets difference between process start and now in microseconds.
     *
     * @return void
     */
    public function getLoadTime()
    {
        return (microtime(true)-DIVERGENCE_START);
    }

    public function handleRequest()
    {
        $main = new Main();
        $response = $main->handle(ServerRequest::fromGlobals());
        (new Emitter($response))->emit();
    }
}
