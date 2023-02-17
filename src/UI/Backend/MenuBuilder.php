<?php

declare(strict_types=1);

namespace App\UI\Backend;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Monofony\Component\Admin\Menu\AdminMenuBuilderInterface;

final class MenuBuilder implements AdminMenuBuilderInterface
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->addConfigurationSubMenu($menu);

        return $menu;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.ui.configuration')
        ;

        $configuration
            ->addChild('backend_admin_user', [
                'route' => 'sylius_backend_admin_user_index',
            ])
            ->setLabel('sylius.ui.admin_users')
            ->setLabelAttribute('icon', 'lock')
        ;
    }
}
