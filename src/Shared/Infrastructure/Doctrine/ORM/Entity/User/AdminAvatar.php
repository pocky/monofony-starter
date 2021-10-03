<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\ORM\Entity\User;

use App\Shared\Infrastructure\Doctrine\ORM\Entity\Media\File;
use Doctrine\ORM\Mapping as ORM;
use Monofony\Contracts\Core\Model\User\AdminAvatarInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity]
#[ORM\Table(name: 'app_admin_avatar')]
class AdminAvatar extends File implements AdminAvatarInterface
{
    /**
     * {@inheritdoc}
     *
     * @Vich\UploadableField(mapping="admin_avatar", fileNameProperty="path")
     *
     * @Assert\File(maxSize="6000000", mimeTypes={"image/*"})
     */
    protected ?\SplFileInfo $file = null;
}
