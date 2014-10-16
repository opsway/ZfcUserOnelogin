<?php

namespace ZfcUserOnelogin\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;


class OneloginLink extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $_serviceLocator;
    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return string
     */
    public function __invoke($options = array())
    {
        $sm = $this->getServiceLocator()->getServiceLocator();
        $config = $sm->get('ZfcUserOnelogin\Config');
        $SAMLsettings = new \OneLogin_Saml2_Settings($config['settings']);
        $idpData = $SAMLsettings->getIdPData();
        $idpSSO = '';
        if (isset($idpData['singleSignOnService']) && isset($idpData['singleSignOnService']['url'])) {
            $idpSSO = $idpData['singleSignOnService']['url'];
            $authnRequest = new \OneLogin_Saml2_AuthnRequest($SAMLsettings);
            $parameters['SAMLRequest'] = $authnRequest->getRequest();
            $idpSSO = \OneLogin_Saml2_Utils::redirect($idpSSO, $parameters, true);
        }
        return '<a href="'.$idpSSO.'"><strong>Login via OneLogin</strong></a>';
    }


    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }
}
