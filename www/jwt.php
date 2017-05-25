<?php

// TODO: this feels clumsy
$globalConfig = \SimpleSAML_Configuration::getInstance();
$vendordir = $globalConfig->resolvePath('vendor');
require_once "$vendordir/autoload.php";

use \Firebase\JWT\JWT;

function get_jwt_key() {
    $filename = \SimpleSAML\Utils\Config::getCertPath("irma-idp-sk.pem");
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
