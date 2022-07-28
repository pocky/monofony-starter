<?php

namespace spec\App\Shared\Infrastructure\Generator;

use App\Shared\Infrastructure\Generator\GeneratorInterface;
use App\Shared\Infrastructure\Generator\UuidGenerator;
use PhpSpec\ObjectBehavior;

class UuidGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UuidGenerator::class);
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_should_generate_an_uuid()
    {
        $this::generate()->shouldBeString();
    }
}
