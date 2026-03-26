<?php
namespace technexus\Models;

/**
 * User model
 * @inheritDoc
 */
class User extends \Divergence\Models\Model
{
    use \Divergence\Models\Relations;

    public static $tableName = 'users';
    private string $Email;
    private string $DisplayName;
    private string $PasswordHash;
    
    public static $relationships = [

    ];
}
