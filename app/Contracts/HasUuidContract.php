<?php

namespace App\Contracts;

interface HasUuidContract
{
    public static function uuidColumnName(): string;

    public function getUuidColumnName(): string;
}
