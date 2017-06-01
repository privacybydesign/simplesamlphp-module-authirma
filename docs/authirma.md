authirma module
================

The authirma module provides support for logging in using IRMA
(https://www.irmacard.org/irmaphone/) through a simplesamlphp authentication module:
`authirma:IRMA`


## Configuration

To create an IRMA authentication source, open
`config/authsources.php` in a text editor, and add an entry for the
authentication source.

For example:

    'irma' => array(
        'authirma:IRMA',
        'irma_api_server' => 'https://example.com',
        'irma_web_server' => 'https://example.com',
        'jwt_privatekeyfile' => 'surfnet-idp-sk.pem',
        'jwt_apiserver_publickeyfile' => 'apiserver-pk.pem',
        "issuer_id" => "my_issuer_id",
        "issuer_displayname" => "my IRMA issuer",
        "requested_attributes" => [
                    [ "label" => "Institute", "attributes" => ["pbdf.pbdf.surfnet.institute"] ],
                    [ "label" => "Type", "attributes" => ["pbdf.pbdf.surfnet.type"] ],
                    [ "label" => "ID", "attributes" => ["pbdf.pbdf.surfnet.id"] ],
                    [ "label" => "Full name", "attributes" => ["pbdf.pbdf.surfnet.fullname"] ],
                    [ "label" => "Given name", "attributes" => ["pbdf.pbdf.surfnet.firstname"] ],
                    [ "label" => "Family name", "attributes" => ["pbdf.pbdf.surfnet.familyname"] ],
                    [ "label" => "Email address", "attributes" => ["pbdf.pbdf.surfnet.email"] ],
                ]
    ),
