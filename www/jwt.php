<?php

// TODO: fix path
//require_once __DIR__ . '/../vendor/autoload.php';
require_once '/Users/jodi/irma-idp/simplesamlphp/vendor/autoload.php';
use \Firebase\JWT\JWT;

if(file_exists('conf.php'))
    include 'conf.php';
if (!defined('ROOT_DIR'))       define('ROOT_DIR', __DIR__ . '/../');
if (!defined('JWT_PRIVATEKEY')) define('JWT_PRIVATEKEY', 'surfnet-idp-sk.pem');

function get_jwt_key() {
// $pk = openssl_pkey_get_private("file://" . ROOT_DIR . $this->jwt_privatekeyfile);
//    if( !isset($this->jwt_privatekeyfile) ) // TODO rename signing key?
//        throw new Exception("JWT signing key missing");
//    $filename = ROOT_DIR . JWT_PRIVATEKEY;
    $filename = "/Users/jodi/irma-idp/simplesamlphp/modules/authirma/surfnet-idp-sk.pem";
    $pk = openssl_pkey_get_private("file://$filename");
    if ($pk === false)
        throw new Exception("Failed to load signing key");
    return $pk;
}


function get_jwt() {
    $pk = get_jwt_key();
    $sprequest = [
        "sub" => "verification_request",
        "iss" => "SURFconext",
        "iat" => time(),
        "sprequest" => [
            "validity" => 60,
            "request" => [
                "content" => [
                    [ "label" => "Institute", "attributes" => ["pbdf.pbdf.surfnet.institute"] ],
                    [ "label" => "Type", "attributes" => ["pbdf.pbdf.surfnet.type"] ],
                    [ "label" => "ID", "attributes" => ["pbdf.pbdf.surfnet.id"] ],
                    [ "label" => "Full name", "attributes" => ["pbdf.pbdf.surfnet.fullname"] ],
                    [ "label" => "Given name", "attributes" => ["pbdf.pbdf.surfnet.firstname"] ],
                    [ "label" => "Family name", "attributes" => ["pbdf.pbdf.surfnet.familyname"] ],
                    [ "label" => "Email address", "attributes" => ["pbdf.pbdf.surfnet.email"] ],
                ]
            ]
        ]
    ];
    return JWT::encode($sprequest, $pk, "RS256", "surfnet_idp");
}

echo get_jwt();
