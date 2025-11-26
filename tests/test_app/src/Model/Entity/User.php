<?php
declare(strict_types=1);

namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

class User extends Entity
{
    protected array $_accessible = [
        '*' => true,
        'id' => false,
    ];

    protected array $_hidden = [
        'password',
    ];
}
