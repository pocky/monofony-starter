<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Slugger;

use Symfony\Component\String\Slugger\AsciiSlugger;

final class Slugger implements SluggerInterface
{
    public static function slugify(string $string, string $separator = '-'): string
    {
        return (new AsciiSlugger())
            ->slug($string, $separator)
            ->lower()
            ->toString();
    }
}
