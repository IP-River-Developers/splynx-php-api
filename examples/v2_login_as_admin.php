<?php
/**
 * Splynx API v2.0 demo script
 * Author: Volodymyr Tsumanchuk (Splynx s.r.o.)
 * https://splynxv2rc.docs.apiary.io - API documentation
 */

include '../src/SplynxApi.php';

$api_url = 'http://splynx/'; // please set your Splynx URL

$admin_login = "ADMIN_LOGIN"; // Splynx administrator login
$admin_password = "ADMIN_PASSWORD"; // Splynx administrator password

$api = new SplynxAPI($api_url);
$api->setVersion(SplynxApi::API_VERSION_2);

$isAuthorized = $api->login([
    'auth_type' => SplynxApi::AUTH_TYPE_ADMIN,
    'login' => $admin_login,
    'password' => $admin_password,
]);

if (!$isAuthorized) {
    exit("Authorization failed!\n");
}

print "<pre>";

print "Authorization info: " . var_export($api->getAuthData(), 1) . "\n";

$locationsApiUrl = "admin/administration/locations";

print "List locations\n";
$result = $api->api_call_get($locationsApiUrl);
print "Result: ";
if ($result) {
    print "Ok!\n";
    print_r($api->response);
} else {
    print "Fail! Error code: $api->response_code\n";
    print_r($api->response);
}
print "\n-------------------------------------------------\n";
