<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Slugger;

use Cocur\Slugify\Slugify;

final class Slugger implements SluggerInterface
{
    public static function slugify(string $string, string $separator = '-'): string
    {
        return (new Slugify())->slugify($string, $separator);
    }
}
