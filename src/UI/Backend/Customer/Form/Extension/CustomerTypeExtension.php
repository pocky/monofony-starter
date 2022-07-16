<?php

declare(strict_types=1);

namespace App\UI\Backend\Customer\Form\Extension;

use App\UI\Backend\Customer\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new AddUserFormSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [CustomerType::class];
    }
}
