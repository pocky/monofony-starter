<?php

namespace spec\App\Shared\Infrastructure\Instrumentation;

use App\Shared\Infrastructure\Instrumentation\Instrumentation;
use App\Shared\Infrastructure\Instrumentation\LoggerInstrumentation;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class LoggerInstrumentationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LoggerInstrumentation::class);
        $this->shouldImplement(Instrumentation::class);
    }

    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_should_return_a_psr_logger()
    {
        $this->getLogger()->shouldImplement(LoggerInterface::class);
    }
}
