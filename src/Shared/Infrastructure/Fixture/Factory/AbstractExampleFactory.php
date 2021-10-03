<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixture\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Remove when Sylius fixtures bundle will include this.
 */
abstract class AbstractExampleFactory implements ExampleFactoryInterface
{
    abstract protected function configureOptions(OptionsResolver $resolver): void;
}
