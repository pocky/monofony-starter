<?php

declare(strict_types=1);

namespace App\Security\Shared\Infrastructure\Persistence\Fixture\Factory;

use App\Security\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity\User\AdminUser;
use Monofony\Contracts\Core\Model\User\AdminUserInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<AdminUser>
 *
 * @method static AdminUser|Proxy createOne(array $attributes = [])
 * @method static AdminUser[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AdminUser|Proxy find(array|mixed|object $criteria)
 * @method static AdminUser|Proxy findOrCreate(array $attributes)
 * @method static AdminUser|Proxy first(string $sortedField = 'id')
 * @method static AdminUser|Proxy last(string $sortedField = 'id')
 * @method static AdminUser|Proxy random(array $attributes = [])
 * @method static AdminUser|Proxy randomOrCreate(array $attributes = [])
 * @method static AdminUser[]|Proxy[] all()
 * @method static AdminUser[]|Proxy[] findBy(array $attributes)
 * @method static AdminUser[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static AdminUser[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method AdminUser|Proxy create(array|callable $attributes = [])
 */
final class AdminUserFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'username' => self::faker()->userName(),
            'enabled' => true,
            'password' => 'password',
            'first_name' => self::faker()->firstName(),
            'last_name' => self::faker()->lastName(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (AdminUserInterface $adminUser) {
                $adminUser->setPlainPassword($adminUser->getPassword());
                $adminUser->setPassword(null);
            })
        ;
    }

    protected static function getClass(): string
    {
        return AdminUser::class;
    }
}
