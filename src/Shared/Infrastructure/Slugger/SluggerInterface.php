<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Slugger;

interface SluggerInterface
{
    public static function slugify(string $string, string $separator = '-'): string;
}
