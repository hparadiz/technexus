<?php
namespace technexus;

use technexus\Models\User;
use Divergence\Models\Auth\Session;

/**
 * Bootstraps site and provides some global references.
 * @inheritDoc
 */
class App extends \Divergence\App
{
    /** @var Session */
    public static $Session;
    
    /**
     * Bootstraps the site.
     *
     * - Sets error handling.
     * - Sets local timezone
     * - Gets or sets Session and stores App::$Session
     * - Checks if login attempt and tries to login
     *
     * @param string $Path
     * @uses static::$Session
     * @return void
     * @inheritDoc
     */
    public static function init($Path)
    {
        error_reporting(E_ALL & ~E_NOTICE);
        parent::init($Path);
        
        date_default_timezone_set('America/New_York');
        
        $tz = (new \DateTime('now', new \DateTimeZone('America/New_York')))->format('P');
        
        \Divergence\IO\Database\MySQL::nonQuery("SET time_zone='$tz';");
        
        static::$Session = Session::getFromRequest();
        static::auth();
    }
    
    /**
     * Checks if login attempt and runs login code.
     *
     * @return void
     */
    public static function auth()
    {
        if ($_POST['login']['email'] && $_POST['login']['password']) {
            static::login($_POST['login']['email'], $_POST['login']['password']);
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
    public static function login($username, $password)
    {
        if ($User = User::getByField('Email', $username)) {
            if (password_verify($password, $User->PasswordHash)) {
                static::$Session->CreatorID = $User->ID;
                static::$Session->save();
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
    public static function is_loggedin()
    {
        if (static::$Session) {
            if (static::$Session->CreatorID) {
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
    public static function getLoadTime()
    {
        return (microtime(true)-DIVERGENCE_START);
    }
}
