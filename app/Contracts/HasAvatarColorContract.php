<?php

namespace App\Contracts;

interface HasAvatarColorContract
{
    public static function avatarColorColumnName(): string;

    public function getAvatarColorColumnName(): string;
}
