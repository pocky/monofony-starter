<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Behat;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;

final readonly class Configuration implements NameInterface, PackageInterface
{
    public function __construct(
        private string $name,
        private string $package,
        private bool $api,
        private string $identifier,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntityName(): string
    {
        return strtolower($this->name);
    }

    public function hasApi(): bool
    {
        return $this->api;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getPackagePath(): string
    {
        return str_replace('\\', '/', $this->package);
    }

    public function getContextPath(): string
    {
        return sprintf('Tests\\Behat\\Context\\UI\\Backend\\%s\\', $this->package);
    }

    public function getApiContextPath(): string
    {
        return sprintf('Tests\\Behat\\Context\\UI\\API\\%s\\', $this->package);
    }

    public function getSetupContextPath(): string
    {
        return sprintf('Tests\\Behat\\Context\\Setup\\%s\\', $this->package);
    }

    public function getPagePath(): string
    {
        return sprintf('Tests\\Behat\\Page\\Backend\\%s\\', $this->package);
    }

    public function getContextTemplatePath(): string
    {
        return 'behat/context/ui/backend/ManagingContext.tpl.php';
    }

    public function getApiContextTemplatePath(): string
    {
        return 'behat/context/ui/api/ManagingContext.tpl.php';
    }

    public function getSetupContextTemplatePath(): string
    {
        return 'behat/context/setup/SetupContext.tpl.php';
    }

    public function getIndexPageTemplatePath(): string
    {
        return 'behat/page/IndexPage.tpl.php';
    }

    public function getCreatePageTemplatePath(): string
    {
        return 'behat/page/CreatePage.tpl.php';
    }

    public function getUpdatePageTemplatePath(): string
    {
        return 'behat/page/UpdatePage.tpl.php';
    }

    public function getFeaturesTemplatePaths(): array
    {
        return [
            'adding' => 'behat/features/backend/adding.tpl.feature',
            'browsing' => 'behat/features/backend/browsing.tpl.feature',
            'deleting' => 'behat/features/backend/deleting.tpl.feature',
            'deleting_multiple' => 'behat/features/backend/deleting_multiple.tpl.feature',
            'editing' => 'behat/features/backend/editing.tpl.feature',
        ];
    }

    public function getApiFeaturesTemplatePaths(): array
    {
        return [
            'adding' => 'behat/features/api/adding.tpl.feature',
            'browsing' => 'behat/features/api/browsing.tpl.feature',
            'deleting' => 'behat/features/api/deleting.tpl.feature',
            'editing' => 'behat/features/api/editing.tpl.feature',
        ];
    }

    public function getFeaturesConfigPath(): string
    {
        return 'behat/config/backend/managing.tpl.yaml';
    }

    public function getApiFeaturesConfigPath(): string
    {
        return 'behat/config/api/managing.tpl.yaml';
    }

    public function getFeaturesDirectory(): string
    {
        return strtolower(str_replace('\\', '/', $this->package));
    }

    public function getEntityPath(): string
    {
        return sprintf('%s\\Shared\\Infrastructure\\Persistence\\Doctrine\\ORM\\Entity\\', $this->package);
    }

    public function getEntityFactoryPath(): string
    {
        return sprintf('%s\\Shared\\Infrastructure\\Persistence\\Fixture\\Factory\\', $this->package);
    }
}
