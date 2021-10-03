<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixture;

use App\Shared\Infrastructure\Fixture\Factory\AdminUserExampleFactory;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class AdminUserFixture extends AbstractResourceFixture
{
    public function __construct(ObjectManager $objectManager, AdminUserExampleFactory $adminUserExampleFactory)
    {
        parent::__construct($objectManager, $adminUserExampleFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'admin_user';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('email')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
        ;
    }
}
