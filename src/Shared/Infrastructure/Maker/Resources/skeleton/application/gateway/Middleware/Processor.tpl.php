<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use <?= $parent; ?>\Request;
use <?= $parent; ?>\Response;

final class Processor
{
    public function __construct(
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return new Response();
    }
}
