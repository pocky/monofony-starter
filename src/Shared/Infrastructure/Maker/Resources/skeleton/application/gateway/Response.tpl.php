<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use App\Shared\Application\Gateway\GatewayResponse;

final class Response implements GatewayResponse
{
    public function __construct(
    ) {
    }

    public function data(): array
    {
        return [];
    }
}
