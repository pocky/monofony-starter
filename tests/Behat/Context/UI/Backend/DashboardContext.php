<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\UI\Backend;

use App\Tests\Behat\Page\Backend\DashboardPage;
use Behat\Behat\Context\Context;

class DashboardContext implements Context
{
    public function __construct(private readonly DashboardPage $dashboardPage)
    {
    }

    /**
     * @When I open administration dashboard
     */
    public function iOpenAdministrationDashboard()
    {
        $this->dashboardPage->open();
    }
}
