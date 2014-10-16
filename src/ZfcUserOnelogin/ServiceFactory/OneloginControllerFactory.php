<?php

namespace ZfcUserOnelogin\ServiceFactory;

use ZfcUserOnelogin\Controller\OneloginController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class OneloginControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $redirectCallback = $controllerManager->getServiceLocator()->get('zfcuser_redirect_callback');

        $controller = new OneloginController($redirectCallback);

        return $controller;
    }
}