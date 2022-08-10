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

        $this->addLogbookSubMenu($menu);
        $this->addConfigurationSubMenu($menu);

        return $menu;
    }

    private function addLogbookSubMenu(ItemInterface $menu): void
    {
        $customer = $menu
            ->addChild('logbook')
            ->setLabel('backend.logbook.ui.menu.title')
        ;

        $customer->addChild('backend_logbook_report', ['route' => 'logbook_backend_report_index'])
            ->setLabel('backend.logbook.ui.menu.item.report')
            ->setLabelAttribute('icon', 'users')
        ;

        $customer->addChild('backend_logbook_entry', ['route' => 'logbook_backend_entry_index'])
            ->setLabel('backend.logbook.ui.menu.item.entry')
            ->setLabelAttribute('icon', 'users')
        ;

        $customer->addChild('backend_logbook_division', ['route' => 'logbook_backend_division_index'])
            ->setLabel('backend.logbook.ui.menu.item.division')
            ->setLabelAttribute('icon', 'users')
        ;

        $customer->addChild('backend_logbook_year', ['route' => 'logbook_backend_year_index'])
            ->setLabel('backend.logbook.ui.menu.item.year')
            ->setLabelAttribute('icon', 'users')
        ;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.ui.configuration')
        ;

        $configuration->addChild('backend_admin_user', ['route' => 'sylius_backend_admin_user_index'])
            ->setLabel('sylius.ui.admin_users')
            ->setLabelAttribute('icon', 'lock')
        ;
    }
}
