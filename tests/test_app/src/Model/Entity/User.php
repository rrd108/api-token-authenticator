<?php

declare(strict_types=1);

namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

class User extends Entity
{
    protected array $accessible = [
        '*' => true,
        'id' => false,
    ];

    protected array $hidden = [
        'password',
    ];
}
