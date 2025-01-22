<?php

namespace App\Enums;

enum RoleEnum: int
{
    case Admin = 1;
    case Author = 2;

    public static function getRoleLabel(int $role)
    {
        switch ($role) {
            case RoleEnum::Admin->value:
                return "Admin";
            case RoleEnum::Author->value:
                return "Author";
            default:
                return $role;
        }
    }
}


?>
