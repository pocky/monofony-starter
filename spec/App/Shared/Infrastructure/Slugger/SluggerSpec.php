<?php

namespace spec\App\Shared\Infrastructure\Slugger;

use App\Shared\Infrastructure\Slugger\Slugger;
use App\Shared\Infrastructure\Slugger\SluggerInterface;
use PhpSpec\ObjectBehavior;

class SluggerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Slugger::class);
        $this->shouldBeAnInstanceOf(SluggerInterface::class);
    }

    function it_should_slugify_a_simple_sentence()
    {
        $this::slugify('A simple sentence')->shouldReturn('a-simple-sentence');
        $this::slugify('A Simple Sentence', '_')->shouldReturn('a_simple_sentence');
    }
}
