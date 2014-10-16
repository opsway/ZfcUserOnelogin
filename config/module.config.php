<?php
return array(
    'controllers' => array(
        'factories' => array(
            'ZfcUserOnelogin\OneloginController' => 'ZfcUserOnelogin\ServiceFactory\OneloginControllerFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zfcuser-onelogin' => array(
                'type'    => 'Literal',
                'priority' => 2000,
                'options' => array(
                    'route' => '/onelogin/auth',
                    'defaults' => array(
                        'controller' => 'ZfcUserOnelogin\OneloginController',
                        'action'     => 'auth',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            //'zfcuser' => __DIR__ . '/../view',
        ),
        'template_map' => array(
               'zfc-user/user/login'           => __DIR__ . '/../view/zfc-user/user/login.phtml',
           ),
    ),
    'service_manager' => array(
        'invokables' => array(
            //'ZfcUserLdap\Adapter\Ldap'                    => 'ZfcUserLdap\Adapter\Ldap',
            'ZfcUserOnelogin\Authentication\Adapter\OneloginAuth' => 'ZfcUserOnelogin\Authentication\Adapter\OneloginAuth',
        ),
        'aliases'    => array(),
        'factories'  => array(
            'ZfcUserOnelogin\Config'                                 => 'ZfcUserOnelogin\ServiceFactory\ConfigFactory',
            'ZfcUserOnelogin\AuthenticationAdapterChain' => 'ZfcUserOnelogin\ServiceFactory\AuthenticationAdapterChainFactory',
            //'ZfcUserLdap\LdapAdapter'                            => 'ZfcUserLdap\ServiceFactory\LdapAdapterFactory',
            //'ZfcUserLdap\LdapConfig'                             => 'ZfcUserLdap\ServiceFactory\LdapConfigFactory',
            //'ZfcUserLdap\Logger'                                 => 'ZfcUserLdap\ServiceFactory\LoggerAdapterFactory',
            //'ZfcUserLdap\Mapper'                                 => 'ZfcUserLdap\ServiceFactory\UserMapperFactory',
            //'ZfcUserLdap\Provider\Identity\LdapIdentityProvider' => 'ZfcUserLdap\Service\LdapIdentityProviderFactory',
            //'ZfcUserLdap\ZfcRbacIdentityProvider'                => 'ZfcUserLdap\ServiceFactory\ZfcRbacIdentityProviderFactory',
        )
    ),
);