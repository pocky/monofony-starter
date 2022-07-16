<?php

declare(strict_types=1);

namespace App\UI\Backend\Customer\Form\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity\User\AppUser;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\FormBuilderInterface;

class AppUserType extends UserType
{
    public function __construct()
    {
        parent::__construct(AppUser::class, ['sylius']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('username')
            ->remove('email');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_app_user';
    }
}
