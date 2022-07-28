<?php
/**
 * Splynx API v2.0 demo script
 * Author: Volodymyr Tsumanchuk (Splynx s.r.o.)
 * https://splynxv2rc.docs.apiary.io - API documentation
 */

include '../src/SplynxApi.php';

$api_url = 'http://splynx/'; // please set your Splynx URL

$customer_id = "CUSTOMER_ID"; // Splynx customer login
$customer_login = "CUSTOMER_LOGIN"; // Splynx customer login
$customer_password = "CUSTOMER_PASSWORD"; // Splynx customer password

$api = new SplynxApi($api_url);
$api->setVersion(SplynxApi::API_VERSION_2);

$isAuthorized = $api->login([
    'auth_type' => SplynxApi::AUTH_TYPE_CUSTOMER,
    'login' => $customer_login,
    'password' => $customer_password,
]);

if (!$isAuthorized) {
    exit("Authorization failed!\n");
}

print "Authorization info: " . var_export($api->getAuthData(), true) . "\n";

$customersApiUrl = "admin/customers/customer";

print "Get customer\n";
$result = $api->api_call_get($customersApiUrl, $customer_id);
print "Result: ";
if ($result) {
    print "Ok!\n";
    print_r($api->response);
} else {
    print "Fail! Error code: $api->response_code\n";
    print_r($api->response);
}
print "\n-------------------------------------------------\n";
