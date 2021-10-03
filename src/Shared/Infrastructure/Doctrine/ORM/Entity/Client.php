<?php

namespace App\Shared\Infrastructure\Doctrine\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_client')]
class Client extends BaseClient
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;
}
