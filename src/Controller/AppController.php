<?php

namespace ApiTokenAuthenticator\Controller;

use Cake\View\JsonView;
use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    public function initialize(): void
    {
        parent::initialize();
        // TODO do we need this here or just in the application?
        $this->loadComponent('Authentication.Authentication');
    }

    public function viewClasses(): array
    {
        // TODO do we need this here or just in the application?
        return [JsonView::class];
    }
}
