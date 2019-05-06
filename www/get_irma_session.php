<?php

/**
 * This page renders the IRMA JWT validation request
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
        "iss" => $source->issuer_id,
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

$verification_jwt = get_jwt($source);
$irma_api_server = $source->irma_api_server;
$url = $source->irma_api_server . "/session";
// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: text/plain\r\n",
        'method'  => 'POST',
        'content' => $verification_jwt
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

header("Content-Type: application/json");

echo $result;
