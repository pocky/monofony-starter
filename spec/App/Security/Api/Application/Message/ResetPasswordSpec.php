<?php

declare(strict_types=1);

namespace spec\App\Security\Api\Application\Message;

use App\Security\Api\Application\Message\ResetPassword;
use PhpSpec\ObjectBehavior;

class ResetPasswordSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('newPassw0rd');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResetPassword::class);
    }

    function it_can_get_password(): void
    {
        $this->password->shouldReturn('newPassw0rd');
    }
}
