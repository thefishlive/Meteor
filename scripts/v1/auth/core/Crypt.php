<?php
namespace meteor\core;

check_env();
define ("HASH_ALGORITHM", PASSWORD_DEFAULT);
define ("HASH_COST", 10);

class Crypt
{
    public static function hash_password($password)
    {
        $hash = password_hash($password, HASH_ALGORITHM, ["cost" => HASH_COST]);
        return $hash;
    }

    public static function check_password($hash, $password)
    {
        return password_verify($password, $hash);
    }
}