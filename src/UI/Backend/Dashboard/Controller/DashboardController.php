<?php

declare(strict_types=1);

namespace App\UI\Backend\Dashboard\Controller;

use App\Shared\UI\Responder\HtmlResponder;
use Monofony\Contracts\Admin\Dashboard\DashboardStatisticsProviderInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class DashboardController
{
    public function __construct(
        private DashboardStatisticsProviderInterface $statisticsProvider,
        private HtmlResponder $htmlResponder,
    ) {
    }

    public function indexAction(): Response
    {
        $statistics = $this->statisticsProvider->getStatistics();

        return ($this->htmlResponder)('backend/index', ['statistics' => $statistics]);
    }
}
