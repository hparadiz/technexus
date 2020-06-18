<?php
/**
 * This file is part of the Divergence package.
 *
 * (c) Henry Paradiz <henry.paradiz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace technexus\Models;

class Session extends \Divergence\Models\Auth\Session
{
    public static $rootClass = __CLASS__;
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];
}
