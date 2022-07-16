<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Security\Api\Customer\Application\Message\ChangeAppUserPassword;
use App\Security\Api\Customer\Application\Message\RegisterAppUser;
use App\Security\Api\Customer\Application\Message\ResetPassword;
use App\Security\Api\Customer\Application\Message\ResetPasswordRequest;
use App\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity\User\AppUser;
use Doctrine\ORM\Mapping as ORM;
use Monofony\Contracts\Core\Model\Customer\CustomerInterface;
use Monofony\Contracts\Core\Model\User\AppUserInterface;
use Sylius\Component\Customer\Model\Customer as BaseCustomer;
use Sylius\Component\Resource\Annotation\SyliusCrudRoutes;
use Sylius\Component\Resource\Annotation\SyliusRoute;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_customer')]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'messenger' => 'input',
            'input' => RegisterAppUser::class,
            'output' => false,
            'openapi_context' => [
                'summary' => 'Registers an app user',
            ],
        ],
        'reset_password_request' => [
            'messenger' => 'input',
            'input' => ResetPasswordRequest::class,
            'output' => false,
            'method' => 'POST',
            'path' => '/request_password',
            'openapi_context' => [
                'summary' => 'Request a new password',
            ],
        ],
        'reset_password' => [
            'messenger' => 'input',
            'input' => ResetPassword::class,
            'output' => false,
            'method' => 'POST',
            'path' => '/reset_password/{token}',
            'openapi_context' => [
                'summary' => 'Reset password',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER") and object.getUser() == user and object == user.getCustomer()',
        ],
        'put' => [
            'security' => 'is_granted("ROLE_USER") and object.getUser() == user and object == user.getCustomer()',
        ],
        'change_password' => [
            'messenger' => 'input',
            'input' => ChangeAppUserPassword::class,
            'output' => false,
            'method' => 'PUT',
            'path' => '/customers/{id}/password',
            'security' => 'is_granted("ROLE_USER")',
            'denormalization_context' => [
                'groups' => ['customer:password:write', ],
            ],
            'openapi_context' => [
                'summary' => 'Change password for logged in customer',
            ],
        ],
    ],
    attributes: [
        'validation_groups' => ['Default', 'sylius', ],
        'pagination_enabled' => false,
        'denormalization_context' => [
            'groups' => ['customer:write', 'user:write', ],
        ],
        'normalization_context' => [
            'groups' => ['customer:read', 'user:read', ],
        ],
    ],
)]
#[SyliusRoute(
    name: 'sylius_backend_customer_show',
    path: '/admin/customers/{id}',
    controller: 'sylius.controller.customer:showAction',
    template: 'backend/customer/show.html.twig',
)]
#[SyliusCrudRoutes(
    alias: 'sylius.customer',
    path: '/admin/customers',
    section: 'backend',
    redirect: 'index',
    templates: 'backend/crud',
    grid: 'sylius_backend_customer',
    except: ['show'],
    vars: [
        'all' => [
            'subheader' => 'sylius.ui.manage_your_customers',
            'templates' => [
                'form' => 'backend/customer/_form.html.twig',
            ],
        ],
        'index' => [
            'icon' => 'users',
        ],
    ],
)]
class Customer extends BaseCustomer implements CustomerInterface
{
    #[ORM\OneToOne(mappedBy: 'customer', targetEntity: AppUser::class, cascade: ['persist'])]
    #[Valid]
    private ?UserInterface $user = null;

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(?UserInterface $user): void
    {
        if ($this->user === $user) {
            return;
        }

        Assert::nullOrIsInstanceOf($user, AppUserInterface::class);

        $previousUser = $this->user;
        $this->user = $user;

        if ($previousUser instanceof AppUserInterface) {
            $previousUser->setCustomer(null);
        }

        if ($user instanceof AppUserInterface) {
            $user->setCustomer($this);
        }
    }
}
