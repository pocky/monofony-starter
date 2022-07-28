<?php

declare(strict_types=1);

namespace App\UI\Backend\Dashboard\Controller;

use App\Shared\UI\Responder\HtmlResponder;
use Monofony\Contracts\Admin\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Resource\Annotation\SyliusRoute;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class DashboardController
{
    public function __construct(
        private readonly DashboardStatisticsProviderInterface $statisticsProvider,
        private readonly HtmlResponder $htmlResponder,
    ) {
    }


    public function indexAction(): Response
    {
        $statistics = $this->statisticsProvider->getStatistics();

        return ($this->htmlResponder)('backend/index', ['statistics' => $statistics]);
    }
}
