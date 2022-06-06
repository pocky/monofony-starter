<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;
use Monofony\Component\Core\Formatter\StringInflector;

final class SharedStorageContext implements Context
{
    public function __construct(private readonly SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Transform /^(it|its|theirs|them)$/
     */
    public function getLatestResource()
    {
        return $this->sharedStorage->getLatestResource();
    }

    /**
     * @Transform /^(?:this|that|the) ([^"]+)$/
     */
    public function getResource($resource)
    {
        return $this->sharedStorage->get(StringInflector::nameToCode($resource));
    }
}
