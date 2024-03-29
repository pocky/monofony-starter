<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\UI\Backend;

use App\Tests\Behat\Page\Backend\Account\LoginPage;
use App\Tests\Behat\Page\Backend\DashboardPage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class LoginContext implements Context
{
    public function __construct(private readonly DashboardPage $dashboardPage, private readonly LoginPage $loginPage)
    {
    }

    /**
     * @Then I should be able to log in as :username authenticated by :password password
     */
    public function iShouldBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        $this->dashboardPage->verify();
    }

    /**
     * @Then I should not be able to log in as :username authenticated by :password password
     */
    public function iShouldNotBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        Assert::true($this->loginPage->hasValidationErrorWith('Error Invalid credentials.'));
        Assert::false($this->dashboardPage->isOpen());
    }

    private function logInAgain(string $username, string $password): void
    {
        $this->dashboardPage->open();
        $this->dashboardPage->logOut();

        $this->loginPage->open();
        $this->loginPage->specifyUsername($username);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
    }
}
