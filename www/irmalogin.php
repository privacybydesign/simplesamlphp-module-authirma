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

error_log(print_r($_REQUEST,true));

if (array_key_exists('jwt_result', $_REQUEST)) {
	$jwt_result = $_REQUEST['jwt_result'];
} else {
	$jwt_result = '';
}
// todo: throw exceptin if missing?

if (!empty($jwt_result)) {
	// attempt to log in
	$errorCode = sspmod_authirma_Auth_Source_IRMA::handleLogin($authStateId, $jwt_result);
} else {
	$errorCode = NULL;
}

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'authirma:irmalogin.php');
$t->data['stateparams'] = array('AuthState' => $authStateId);
$t->data['errorcode'] = $errorCode;
$t->data['logo_url'] = SimpleSAML\Module::getModuleURL('authirma/resources/irma.png');
$t->data['resources_url'] = SimpleSAML\Module::getModuleURL('authirma/resources');

$t->data['errorcodes'] = SimpleSAML\Error\Errorcodes::getAllErrorCodeMessages();
$t->data['errorcodes']['title']['IRMA_INVALIDCREDENTIALS'] = '{authirma:irma:title_error_invalid}';
$t->data['errorcodes']['title']['IRMA_EXPIREDCREDENTIALS'] = '{authirma:irma:title_error_expired}';
$t->data['errorcodes']['descr']['IRMA_INVALIDCREDENTIALS'] = '{authirma:irma:descr_error_invalid}';
$t->data['errorcodes']['descr']['IRMA_EXPIREDCREDENTIALS'] = '{authirma:irma:descr_error_expired}';

$t->show();
exit();
