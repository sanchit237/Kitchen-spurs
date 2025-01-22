<?php

namespace App\Enums;

enum ArticleStatusEnum: int
{
    case Draft = 1;
    case Published = 2;
    case Archived = 3;

    public static function getStatusLabel(int $status)
    {
        switch ($status) {
            case ArticleStatusEnum::Draft->value:
                return "Draft";
            case ArticleStatusEnum::Published->value:
                return "Published";
            case ArticleStatusEnum::Archived->value:
                return "Archived";
            default:
                return $status;
        }
    }
}


?>
