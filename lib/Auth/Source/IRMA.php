<?php

/**
 * This class implements IRMA authentication
 * using the IRMA API
 *
 * @author Joost van Dijk <vandijk.joost@gmail.com>
 * @author Sietse Ringers <>
 * @package SimpleSAMLphp
 */
class sspmod_authirma_Auth_Source_IRMA extends SimpleSAML_Auth_Source {


    /**
     * Constructor for this authentication source.
     *
     * All subclasses who implement their own constructor must call this
     * constructor before using $config for anything.
     *
     * @param array $info  Information about this authentication source.
     * @param array &$config  Configuration for this authentication source.
     */
    public function __construct($info, &$config) {
        assert('is_array($info)');
        assert('is_array($config)');
        // TODO add config params
        parent::__construct($info, $config);
        return;
    }


    /**
     * Finish a failed authentication.
     *
     * This function can be overloaded by a child authentication
     * class that wish to perform some operations on failure
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authFailed(&$state) {
        $config = SimpleSAML_Configuration::getInstance();

        $t = new SimpleSAML_XHTML_Template($config,
            'authX509:X509error.php');
        $t->data['errorcode'] = $state['authirma.error'];

        $t->show();
        exit();
    }


    /**
     * Validate IRMA credential and login
     *
     * This function try to validate the certificate.
     * On success, the user is logged in without going through
     * a login page.
     * On failure, The authirma:IRMAerror.php template is
     * loaded.
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authenticate(&$state) {
        assert('is_array($state)');

        if (false) { // TODO error condition
            $state['authirma.error'] = "SOMEERROR";
            $this->authFailed($state);
            assert('FALSE'); // NOTREACHED
            return;
        }

        // TODO: verify IRMA credentials

        SimpleSAML_Logger::info('authirma: authentication succesful');
        $attributes = array();
        $attributes['irma_id'] = array('test');
        $state['Attributes'] = SimpleSAML\Utils\Attributes::normalizeAttributesArray($attributes);
        $this->authSuccesful($state);

        assert('FALSE'); /* NOTREACHED */
        return;

    }

    /**
     * Finish a succesfull authentication.
     *
     * This function can be overloaded by a child authentication
     * class that wish to perform some operations after login.
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authSuccesful(&$state) {
        SimpleSAML_Auth_Source::completeAuth($state);

        assert('FALSE'); /* NOTREACHED */
        return;
    }


}
