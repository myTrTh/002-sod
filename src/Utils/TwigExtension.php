<?php

namespace App\Utils;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use App\Utils\TokenManager;
use App\Utils\UserManager;
use App\Utils\Dater;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    private $tokenManager;
    private $container;
    private $userManager;

    public function __construct($container)
    {
        $this->container = $container;

        $this->tokenManager = new TokenManager();
        $this->userManager = new userManager($container);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token', array($this, 'generateCSRFtoken')),
            new TwigFunction('isAccess', array($this, 'isAccess')),
            new TwigFunction('isUserAccess', array($this, 'isUserAccess')),
            new TwigFunction('isPermission', array($this, 'isPermission')),
            new TwigFunction('isUserPermission', array($this, 'isUserPermission')),
            new TwigFunction('hierarchyAccess', array($this, 'hierarchyAccess')),
            new TwigFunction('textMode', array($this, 'textMode')),
            new TwigFunction('beautiful_date', array($this, 'beautiful_date')),
            new TwigFunction('getPageNumber', array($this, 'getPageNumber')),
        ];
    }

    public function getGlobals()
    {
        return [
            'app' => [
                'user' => $this->userManager->getUser(),
                'path' => $this->container['assets']->assets()
            ]
        ];
    }

    public function generateCSRFtoken()
    {
        return $this->tokenManager->generateCSRFtoken();
    }

    public function isAccess($access_role)
    {
        return $this->userManager->isAccess($access_role);
    }

   public function isUserAccess($id, $access_role)
    {
        return $this->userManager->isUserAccess($id, $access_role);
    }    

    public function isPermission($access_permission)
    {
        return $this->userManager->isPermission($access_permission);
    }

    public function isUserPermission($id, $access_permission)
    {
        return $this->userManager->isUserPermission($id, $access_permission);
    }

   public function hierarchyAccess($id)
    {
        return $this->userManager->hierarchyAccess($id);
    }

    public function textMode($message)
    {
        return $this->container['textMode']->textMode($message);
    }

    public function beautiful_date($date)
    {
        return $this->container['dater']->beautiful_date($date);
    }

    public function getPageNumber()
    {
        return $this->container['controller']->getPageNumber();
    }
}