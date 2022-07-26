<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Persistence\Doctrine\ORM\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Monofony\Contracts\Core\Model\User\AdminAvatarInterface;
use Monofony\Contracts\Core\Model\User\AdminUserInterface;
use Sylius\Component\Resource\Annotation\SyliusCrudRoutes;
use Sylius\Component\User\Model\User as BaseUser;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_admin_user')]
#[SyliusCrudRoutes(
    alias: 'sylius.admin_user',
    path: '/admin/users',
    section: 'backend',
    redirect: 'index',
    templates: 'backend/crud',
    grid: 'sylius_backend_admin_user',
    except: ['show'],
    vars: [
        'all' => [
            'subheader' => 'sylius.ui.manage_users_able_to_access_administration_panel',
            'templates' => [
                'form' => 'backend/admin_user/_form.html.twig',
            ],
        ],
        'index' => [
            'icon' => 'lock',
        ],
    ],
)]
class AdminUser extends BaseUser implements AdminUserInterface
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstName = null;

    #[ORM\OneToOne(targetEntity: AdminAvatar::class, cascade: ['persist'])]
    private ?AdminAvatarInterface $avatar = null;

    public function __construct()
    {
        parent::__construct();

        $this->roles = [self::DEFAULT_ADMIN_ROLE];
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getAvatar(): ?AdminAvatarInterface
    {
        return $this->avatar;
    }

    public function setAvatar(?AdminAvatarInterface $avatar): void
    {
        $this->avatar = $avatar;
    }
}
