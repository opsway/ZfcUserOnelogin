<?php
namespace ZfcUserOnelogin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;

class OneloginController extends AbstractActionController
{

    /**
     * @var callable $redirectCallback
     */
    protected $redirectCallback;

    /**
     * @param callable $redirectCallback
     */
    public function __construct($redirectCallback)
    {
        if (!is_callable($redirectCallback)) {
            throw new \InvalidArgumentException('You must supply a callable redirectCallback');
        }
        $this->redirectCallback = $redirectCallback;
    }

    public function authAction()
    {
       // if (!$this->hybridAuth) {
        //    // This is likely user that cancelled login...
         //   return $this->redirect()->toRoute('zfcuser/login');
        //}

        // For provider authentication, change the auth adapter in the ZfcUser Controller Plugin
        $this->zfcUserAuthentication()->setAuthAdapter($this->getServiceLocator()->get('ZfcUserOnelogin\AuthenticationAdapterChain'));

        // Adding the provider to request metadata to be used by HybridAuth adapter
        //$this->getRequest()->setMetadata('provider', $provider);

        // Forward to the ZfcUser Authenticate action
        return $this->forward()->dispatch('zfcuser', array('action' => 'authenticate'));
    }

}
