<?php
namespace technexus;

use \technexus\Models\User as User;
use \technexus\Models\Session as Session;

class App extends \Divergence\App
{
    public static $Session;
    
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
    
    public static function auth()
    {
        if ($_POST['login']['email'] && $_POST['login']['password']) {
            static::login($_POST['login']['email'], $_POST['login']['password']);
        }
    }
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
    
    public static function is_loggedin()
    {
        if (static::$Session) {
            if (static::$Session->CreatorID) {
                return true;
            }
        }
        return false;
    }
    
    public static function getLoadTime()
    {
        return (microtime(true)-DIVERGENCE_START);
    }
}
