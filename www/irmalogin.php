<?php

/**
 * This page shows a username/password login form, and passes information from it
 * to the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

if (!array_key_exists('AuthState', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];

if (array_key_exists('irma_result', $_REQUEST)) {
	$irma_result = $_REQUEST['irma_result'];
} else {
	$irma_result = '';
}

if (!empty($irma_result)) {
	// attempt to log in
	$errorCode = sspmod_authirma_Auth_Source_IRMA::handleLogin($authStateId, $irma_result);
} else {
	$errorCode = NULL;
}

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'authirma:irmalogin.php');
$t->data['stateparams'] = array('AuthState' => $authStateId);
$t->data['errorcode'] = $errorCode;
$t->data['errorcodes'] = SimpleSAML\Error\Errorcodes::getAllErrorCodeMessages();
$t->data['logo_url'] = SimpleSAML\Module::getModuleURL('authirma/resources/irma.png');
$t->show();
exit();
