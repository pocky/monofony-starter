<?php

declare(strict_types=1);

namespace spec\App\Shared\Infrastructure\Templating;

use App\Shared\Infrastructure\Templating\TemplatingInterface;
use App\Shared\Infrastructure\Templating\Twig;
use PhpSpec\ObjectBehavior;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Twig::class);
        $this->shouldImplement(TemplatingInterface::class);
    }

    function let(Environment $environment)
    {
        $loader = new ArrayLoader([
            'index' => 'hello {{ name }}',
        ]);

        $environment->setLoader($loader);
        $environment->render('index', ['name' => 'you'])->willReturn('hello you');

        $this->beConstructedWith($environment);
    }

    public function it_should_render_a_twig_template(Environment $environment)
    {
        $this->render('index', ['name' => 'you'])->shouldBeString();
    }
}
