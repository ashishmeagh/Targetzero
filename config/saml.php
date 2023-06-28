<?php

$urlManager = Yii::$app->urlManager;

$spBaseUrl = "https://dev.targetzerowt.com/";

return [
    'sp' => [
        'entityId' => $spBaseUrl.'saml/metadata',
        'assertionConsumerService' => [
            'url' => $spBaseUrl.'saml/acs',
        ],
        'singleLogoutService' => [
            'url' => $spBaseUrl.'saml/sls',
        ],
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    ],
    'idp' => [
        'entityId' => 'https://sts.windows.net/e05675ba-c568-489a-a43d-4cf3830c6af0/',
                    'singleSignOnService' => [
                        'url' => 'https://login.microsoftonline.com/e05675ba-c568-489a-a43d-4cf3830c6af0/saml2',
                    ],
                    'singleLogoutService' => [
                        'url' => 'https://login.microsoftonline.com/e05675ba-c568-489a-a43d-4cf3830c6af0/saml2',
                    ],
        'x509cert' => '-----BEGIN CERTIFICATE-----
MIIC8DCCAdigAwIBAgIQcS2d0WsXDbdOxflIHvX1BjANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQD
EylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMjAxMTExNDM4
MzNaFw0yNTAxMTExNDM4MzNaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQg
U1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzRiraRia9lNi
mJKUlZbRl+wtB3GUun3hahkUllrwhcLJjL1MgjvU6QL3YnDlCysfVgg05NWuid5X71E/zL7PBLVU
kPiuIgyqlw0Ahhv+jrnDUcPg8YsnEHvboghD4YRZZzviVuATHNVbAFStedB+Yemz3QGfeoOOBTun
7ymUO2jO+3XA8wblTctg9RK8Wb3mOtdEFf7Pn+/iakhjLNVbBpUmVneGxTCGfI06+sXPvk9vPyRf
yfEMhHKbGt1+rLa3Nqyo94avvkX5aQmIYZO97HUYq+CMe9NI6qIWQINe+8cXPQifm5bPOo2ZJNRg
wVdYlMI5thE8N2RH8CNAVgNuEQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAeYFXAczeNjYpnLCNc
h0YkyIfrF3PzRpZG+h6hnaXVrBPl7kEL4gX7ZTV6OJK9ch5czgXam2HOWL/nF2SBC6ZKINZVdD+Z
0MU2tRwUE3iIONgtc+eVb4Wkcs4aIauxQN8TPH7Q6+CZV0s4w4ZrFbg/AKzGTHhIIDehbBGGeigJ
8SzRyr/47PMK/V8y7p3qy12KgYwzkSudneJmufjCHajzfRnH99kSz9MjYEod4yt0MEKeMWM7rSLf
GjvcQj+9EEvjjybSwUhGmir1OOQHFqMFDh8LwVe2tmI6OqJgaQHOCiBxJTFXt7VsXm8W5A3F5Wpl
g6ILAYxroqNSRJrp0/ao
-----END CERTIFICATE-----',
    ],
];