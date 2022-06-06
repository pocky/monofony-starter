<?php

declare(strict_types=1);

namespace App\UI\Backend\Controller\Dashboard;

use Monofony\Contracts\Admin\Dashboard\DashboardStatisticsProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class DashboardController
{
    public function __construct(
        private readonly DashboardStatisticsProviderInterface $statisticsProvider,
        private readonly Environment $twig,
    ) {
    }

    public function indexAction(): Response
    {
        $statistics = $this->statisticsProvider->getStatistics();
        $content = $this->twig->render('backend/index.html.twig', ['statistics' => $statistics]);

        return new Response($content);
    }
}
