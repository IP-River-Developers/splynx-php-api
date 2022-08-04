<?php // phpcs:ignoreFile
/**
 * Splynx API v2.0 demo script
 * Author: Volodymyr Tsumanchuk (Splynx s.r.o.)
 * https://splynxv2rc.docs.apiary.io - API documentation
 */

include '../src/SplynxApi.php';

$api_url = 'http://splynx/'; // please set your Splynx URL

$session_id= "SESSION_ID"; // Splynx session id

$api = new SplynxApi($api_url);
$api->setVersion(SplynxApi::API_VERSION_2);

$isAuthorized = $api->login([
    'auth_type' => SplynxApi::AUTH_TYPE_SESSION,
    'session_id' => $session_id,
]);

if (!$isAuthorized) {
    exit("Authorization failed!\n");
}

print "Authorization info: " . var_export($api->getAuthData(), true) . "\n";

$customersApiUrl = "admin/customers/customer";

print "Get customers\n";
$result = $api->api_call_get($customersApiUrl);
print "Result: ";
if ($result) {
    print "Ok!\n";
    print_r($api->response);
} else {
    print "Fail! Error code: $api->response_code\n";
    print_r($api->response);
}
print "\n-------------------------------------------------\n";
