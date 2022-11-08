<?php

namespace App\Helper;

class Constants
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const DRIVE_FOLDERS_HIERARCHY = [
        'name' => 'pmfes',
        'dataType' => 'folder',
        'children' => [
            ['name' => 'images', 'dataType' => 'image'],
        ]
    ];

    const DRIVE_IMAGES_FOLDER = 'images';
    const DRIVE_IMAGES_DATATYPE = 'image';    

    public static function getUserTypes(): array
    {
        return [self::ROLE_ADMIN];
    }
}
