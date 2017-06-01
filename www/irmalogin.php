<?php

/**
 * This page shows a username/password login form, and passes information from it
 * to the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
**/

use \Firebase\JWT\JWT;

function get_jwt_key($source) {
	$filename = \SimpleSAML\Utils\Config::getCertPath($source->jwt_privatekeyfile);
	$pk = openssl_pkey_get_private("file://$filename");
	if ($pk === false)
		throw new Exception("Failed to load signing key");
	return $pk;
}

function get_jwt($source) {
	$sprequest = [
		"sub" => "verification_request",
		"iss" => $source->issuer_displayname,
		"iat" => time(),
		"sprequest" => [
			"validity" => 60,
			"request" => [
				"content" => $source->requested_attributes
			]
		]
	];
	return JWT::encode($sprequest, get_jwt_key($source), "RS256", $source->issuer_id);
}

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
$t->data['verification_jwt'] = get_jwt($source);
$t->data['irma_api_server'] = $source->irma_api_server;
$t->data['irma_web_server'] = $source->irma_web_server;

$t->data['errorcodes'] = SimpleSAML\Error\Errorcodes::getAllErrorCodeMessages();
$t->data['errorcodes']['title']['IRMA_INVALIDCREDENTIALS'] = '{authirma:irma:title_error_invalid}';
$t->data['errorcodes']['title']['IRMA_EXPIREDCREDENTIALS'] = '{authirma:irma:title_error_expired}';
$t->data['errorcodes']['descr']['IRMA_INVALIDCREDENTIALS'] = '{authirma:irma:descr_error_invalid}';
$t->data['errorcodes']['descr']['IRMA_EXPIREDCREDENTIALS'] = '{authirma:irma:descr_error_expired}';

$t->show();
exit();
