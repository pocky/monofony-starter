<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IdentifiableTrait
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
