<?php

declare(strict_types=1);

namespace spec\App\Shared\Infrastructure\Clock;

use App\Shared\Infrastructure\Clock\Clock;
use PhpSpec\ObjectBehavior;

class ClockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Clock::class);
    }

    function it_should_return_an_immutable_datetime()
    {
        $this->now()->shouldBeAnInstanceOf(\DateTimeImmutable::class);
    }
}
