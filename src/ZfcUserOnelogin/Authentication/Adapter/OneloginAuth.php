<?php
namespace ZfcUserOnelogin\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcUser\Authentication\Adapter\AbstractAdapter;
use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;
//use ZfcUserLdap\Mapper\User as UserMapperInterface;
use ZfcUser\Options\AuthenticationOptionsInterface;
//use ZfcUserLdap\Mapper\UserHydrator;
use Zend\Validator\EmailAddress;
use Zend\Authentication\Exception\UnexpectedValueException as UnexpectedExc;

class OneloginAuth extends AbstractAdapter implements ServiceManagerAwareInterface
{
    /**
     * @var UserMapperInterface
     */
    protected $mapper;
    /**
     * @var closure / invokable object
     */
    protected $credentialPreprocessor;
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $options;
    /** @var ZfcUser\Entity\User */
    protected $entity;

    protected $configModule;

    public function authenticate(AuthEvent $e)
    {
        $userObject = null;
        $config = $this->getConfigModule();
        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $e->setIdentity($storage['identity'])
                ->setCode(AuthenticationResult::SUCCESS)
                ->setMessages(array('Authentication successful.'));
            return;
       }


        $auth = new \OneLogin_Saml2_Auth($config['settings']);
        $auth->processResponse();

        $errors = $auth->getErrors();
        if (!empty($errors)) {
            throw new UnexpectedExc('Saml error response returned: ' . var_export($errors, true));
        }

        if (!$auth->isAuthenticated()) {
            $e->setCode(AuthenticationResult::FAILURE)
                ->setMessages(array('Not Authentificate in OneLogin.'));
            $this->setSatisfied(false);

            return false;
        }

        $samlAttribute = $auth->getAttributes();

        $userObject = $this->findLocalUser($samlAttribute);

        if (!$userObject) {
            $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                ->setMessages(array('A record with the supplied identity could not be found.'));
            $this->setSatisfied(false);

            return false;
        }

        if ($this->getOptions()->getEnableUserState()) {
// Don't allow user to login if state is not in allowed list
            if (!in_array($userObject->getState(), $this->getOptions()->getAllowedLoginStates())) {
                $e->setCode(AuthenticationResult::FAILURE_UNCATEGORIZED)
                    ->setMessages(array('A record with the supplied identity is not active.'));
                $this->setSatisfied(false);

                return false;
            }
        }

        $e->setIdentity($userObject);
        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $userObject;
        $this->getStorage()->write($storage);
        $e->setCode(AuthenticationResult::SUCCESS)
            ->setMessages(array('Authentication successful.'));

    }

    protected function findLocalUser($samlAttribute){
        $zfcUser = $this->getMapper()->findByEmail($samlAttribute['User.email'][0]);
        if (!$zfcUser){
            $zfcUser = $this->getMapper()->findByUsername($samlAttribute['User.Username'][0]);
        }
        $config = $this->getConfigModule();
        if (!$zfcUser && $config['auto-registration']){
            $zfcUser = $this->getEntity();
            $zfcUser->setEmail($samlAttribute['User.email'][0])
                ->setUsername($samlAttribute['User.Username'][0])
                ->setDisplayName($samlAttribute['User.FirstName'][0]. ' ' .  $samlAttribute['User.LastName'][0])
                ->setPassword('onelogin-registration')
                ->setState(1);
            $zfcServiceEvents = $this->getServiceManager()->get('zfcuser_user_service')->getEventManager();
            $zfcServiceEvents->trigger('register', $this, array('user' => $zfcUser));
            $this->getMapper()->insert($zfcUser);
        }

        return $zfcUser;
    }

    public function getConfigModule()
    {
        if (null == $this->configModule) {
            $this->configModule = $this->serviceManager->get('ZfcUserOnelogin\Config');
        }
        return $this->configModule;
    }

    /**
     * getMapper
     *
     * @return UserMapperInterface
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('zfcuser_user_mapper');
        }

        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @param UserMapperInterface $mapper
     *
     * @return LdapAuth
     */
    public function setMapper(UserMapperInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     *
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param AuthenticationOptionsInterface $options
     */
    public function setOptions(AuthenticationOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @return AuthenticationOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof AuthenticationOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('zfcuser_module_options'));
        }

        return $this->options;
    }

    /**
     * @return AuthenticationOptionsInterface
     */
    public function getEntity()
    {
        $entityClass = $this->getOptions()->getUserEntityClass();
        $this->entity = new $entityClass;

        return $this->entity;
    }
}