<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Entity;

use App\Enum\Permissions;
use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\FlagBag;

#[ORM\Entity]
#[ORM\Table(name: 'access_rights')]
class AccessRight
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $uuid;

    /** @var FlagBag<Permissions>|null */
    #[ORM\Column(type: 'permissions_flagbag', nullable: true)]
    private ?FlagBag $permissions;

    public function __construct(string $uuid, ?FlagBag $permissions = null)
    {
        $this->uuid = $uuid;
        $this->permissions = $permissions;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPermissions(): ?FlagBag
    {
        return $this->permissions;
    }
}
