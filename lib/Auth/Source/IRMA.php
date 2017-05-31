<?php

use \Firebase\JWT\JWT;

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
     * The string used to identify our states.
     */
    const STAGEID = 'sspmod_authirma_Auth_Source_IRMA.state';

    /**
     * The key of the AuthId field in the state.
     */
    const AUTHID = 'sspmod_authirma_Auth_Source_IRMA.AuthId';

    /**
     * The client id/key for use with the Auth_Yubico PHP module.
     */
    private $jwt_privatekeyfile;

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
        parent::__construct($info, $config);
        // config params
        if (array_key_exists('jwt_privatekeyfile', $config)) {
            $this->jwt_privatekeyfile = $config['jwt_privatekeyfile'];
        }
        return;
    }


    /**
     * Validate IRMA credential and login
     *
     * This function tries to validate IRMA credentials.
     * On success, the user is logged in without going through
     * a login page.
     * On failure, The authirma:IRMAerror.php template is
     * loaded.
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authenticate(&$state) {
        assert('is_array($state)');

        // We are going to need the authId in order to retrieve this authentication source later
        $state[self::AUTHID] = $this->authId;
        $id = SimpleSAML_Auth_State::saveState($state, self::STAGEID);
        $url = SimpleSAML\Module::getModuleURL('authirma/irmalogin.php');
        \SimpleSAML\Utils\HTTP::redirectTrustedURL($url, array('AuthState' => $id));
    }


    /**
     * Handle login request.
     *
     * This function is used by the login form (www/irmalogin.php) when the user
     * submits IRMA credentials. On success, it will not return.
     * On failure, it will return the error code. Other failures will throw an
     * exception.
     *
     * @param string $authStateId  The identifier of the authentication state.
     * @param string $irma_result  JWT received from API server
     * @return string  Error code in the case of an error.
     */
    public static function handleLogin($authStateId, $irma_result) {
        assert('is_string($authStateId)');
        assert('is_string($irma_result)');
        /* Retrieve the authentication state. */
        $state = SimpleSAML_Auth_State::loadState($authStateId, self::STAGEID);
        /* Find authentication source. */
        assert('array_key_exists(self::AUTHID, $state)');
        $source = SimpleSAML_Auth_Source::getById($state[self::AUTHID]);
        if ($source === NULL) {
            throw new Exception('Could not find authentication source with id ' . $state[self::AUTHID]);
        }
        try {
            /* Attempt to log in. */
            $attributes = $source->login($irma_result);
        } catch (SimpleSAML_Error_Error $e) {
            /* An error occurred during login. Check if it is because of the wrong
             * username/password - if it is, we pass that error up to the login form,
             * if not, we let the generic error handler deal with it.
             */
            if ($e->getErrorCode() === 'IRMA_INVALIDCREDENTIALS'
                || $e->getErrorCode() === 'IRMA_EXPIREDCREDENTIALS') { // TODO
                return $e->getErrorCode();
            }
            /* Some other error occurred. Rethrow exception and let the generic error
             * handler deal with it.
             */
            throw $e;
        }

        $state['Attributes'] = SimpleSAML\Utils\Attributes::normalizeAttributesArray($attributes);
        SimpleSAML_Auth_Source::completeAuth($state);
    }


    /**
     * Attempt to log in using IRMA credentials.
     *
     * On a successful login, this function should return the users attributes. On failure,
     * it should throw an exception. If the error was due to invalid IRMA credentials,
     * a SimpleSAML_Error_Error('IRMA_INVALIDCREDENTIALS') should be thrown.
     *
     * @param string $irma_result  The JWT token from the IRMA API server.
     * @return array  Associative array with the users attributes.
     */
    protected function login($irma_credential) {
        assert('is_string($irma_credential)');

        $pubkeyfile = \SimpleSAML\Utils\Config::getCertPath("apiserver-pk.pem");
        $pubkey = openssl_pkey_get_public("file://$pubkeyfile");
        if( !$pubkey )
            throw new SimpleSAML_Error_Error('INVALIDCERT'); //  TODO irma-specific error here

        try {
            // validate IRMA credentials
            $decoded = (array) JWT::decode($irma_credential,$pubkey,array('RS256'));
            if ($decoded["status"] === "EXPIRED")
                throw new SimpleSAML_Error_Error('IRMA_EXPIREDCREDENTIALS');
            elseif ($decoded["status"] !== "VALID")
                throw new SimpleSAML_Error_Error('IRMA_INVALIDCREDENTIALS');
            $attributes = (array) $decoded['attributes'];
        } catch (SimpleSAML_Error_Error $e) {
            throw $e;
        } catch (Exception $e) {
            SimpleSAML\Logger::info('authirma:' . $this->authId . ': Validation error (IRMA credential ' . $irma_credential . ')');
            throw new SimpleSAML_Error_Error('IRMA_INVALIDCREDENTIALS', $e);
        }
        SimpleSAML\Logger::info('authirma:' . $this->authId . ': IRMA credential ' . $irma_credential . ' validated successfully');
        return $attributes;
    }


    /**
     * Finish a failed authentication.
     *
     * This function can be overloaded by a child authentication
     * class that wish to perform some operations on failure
     *
     * @param array &$state  Information about the current authentication.
     */
    public function authFailed(&$state) { // TODO
//        $state['authirma.error'] = "SOMEERROR";

        $config = SimpleSAML_Configuration::getInstance();


        $t = new SimpleSAML_XHTML_Template($config,
            'authirma:IRMAerror.php');  // TODO
        $t->data['errorcode'] = $state['authirma.error'];

        $t->show();
        exit();
    }
}
