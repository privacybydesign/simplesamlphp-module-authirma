<?php

/**
 * This page shows a username/password login form, and passes information from it
 * to the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
**/

if (!array_key_exists('AuthState', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];

$state = SimpleSAML_Auth_State::loadState($authStateId, sspmod_authirma_Auth_Source_IRMA::STAGEID);
assert('array_key_exists(sspmod_authirma_Auth_Source_IRMA::AUTHID, $state)');
$source = SimpleSAML_Auth_Source::getById($state[sspmod_authirma_Auth_Source_IRMA::AUTHID]);
if ($source === NULL) {
	throw new Exception('Could not find authentication source with id ' . $state[sspmod_authirma_Auth_Source_IRMA::AUTHID]);
}

if (array_key_exists('jwt_result', $_REQUEST)) {
	$jwt_result = $_REQUEST['jwt_result'];
} else {
	$jwt_result = '';
}

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

if (!isset($t->data['head']))
	$t->data['head'] = '';
$t->data['head'] .= <<<IRMAHEADERS
<meta name="irma-api-server" value="{$source->irma_api_server}">
<script type="text/javascript" src="{$t->data['resources_url']}/jquery-3.4.0.min.js"></script>
<script type="text/javascript" src="{$t->data['resources_url']}/irma.js"></script>
<script type="text/javascript" src="{$t->data['resources_url']}/verify.js"></script>
<script type="text/javascript"> 
var irma_api_server = "{$source->irma_api_server}"; 
var authStateId = "$authStateId"; 
</script>
IRMAHEADERS;

$t->data['errorcodes'] = SimpleSAML\Error\ErrorCodes::getAllErrorCodeMessages();
$t->data['errorcodes']['title']['RESPONSESTATUSNOSUCCESS'] = '{authirma:irma:title_error_invalid}';
$t->data['errorcodes']['title']['USERABORTED'] = '{authirma:irma:title_error_expired}';
$t->data['errorcodes']['descr']['RESPONSESTATUSNOSUCCESS'] = '{authirma:irma:descr_error_invalid}';
$t->data['errorcodes']['descr']['USERABORTED'] = '{authirma:irma:descr_error_expired}';

$t->show();
exit();
