<?php

declare(strict_types=1);

namespace App\Customer\Shared\Infrastructure\Form\Type;

use App\Customer\Shared\Infrastructure\Form\EventSubscriber\CustomerRegistrationFormSubscriber;
use App\Security\Shared\Infrastructure\Form\Type\AppUserRegistrationType;
use App\Shared\Infrastructure\Doctrine\ORM\Entity\Customer\Customer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class CustomerSimpleRegistrationType extends AbstractResourceType
{
    public function __construct(
        private readonly RepositoryInterface $customerRepository,
    ) {
        parent::__construct(Customer::class, [
            'sylius',
            'sylius_user_registration',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.form.customer.email',
            ])
            ->add('user', AppUserRegistrationType::class, [
                'label' => false,
                'constraints' => [new Valid()],
            ])
            ->addEventSubscriber(new CustomerRegistrationFormSubscriber($this->customerRepository))
            ->setDataLocked(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_customer_simple_registration';
    }
}
