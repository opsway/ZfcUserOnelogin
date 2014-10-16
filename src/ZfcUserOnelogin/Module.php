<?php
namespace ZfcUserOnelogin;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Zend\EventManager\EventInterface;
use Zend\Form\FormElementManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module implements BootstrapListenerInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface
   // ServiceProviderInterface
{

    public function onBootstrap(EventInterface $e)
    {
        /* @var $application \Zend\Mvc\Application */
        $application         = $e->getTarget();
        $eventManager        = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                //'zfcUserIdentity' => function ($sm) {
                //    $locator = $sm->getServiceLocator();
                //    $viewHelper = new View\Helper\ZfcUserIdentity;
                //    $viewHelper->setAuthService($locator->get('zfcuser_auth_service'));
                //    return $viewHelper;
                //},
                'zfcUserOneloginLink' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\OneloginLink;
                    $viewHelper->setServiceLocator($locator);
                    //$viewHelper->setLoginForm($locator->get('zfcuser_login_form'));
                    return $viewHelper;
                },
            ),
        );

    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}