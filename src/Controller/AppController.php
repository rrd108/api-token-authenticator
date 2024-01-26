<?php
declare(strict_types=1);

namespace ApiTokenAuthenticator\Controller;

use App\Controller\AppController as BaseController;
use Cake\View\JsonView;

class AppController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        // TODO do we need this here or just in the application?
        $this->loadComponent('Authentication.Authentication');
    }

    /**
     * @inheritDoc
     */
    public function viewClasses(): array
    {
        // TODO do we need this here or just in the application?
        return [JsonView::class];
    }
}
