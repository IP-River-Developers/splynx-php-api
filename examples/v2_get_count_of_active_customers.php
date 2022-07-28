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

$api = new SplynxApi($api_url);
$api->setVersion(SplynxApi::API_VERSION_2);

$isAuthorized = $api->login([
    'auth_type' => SplynxApi::AUTH_TYPE_ADMIN,
    'login' => $admin_login,
    'password' => $admin_password,
]);

if (!$isAuthorized) {
    exit("Authorization failed!\n");
}

$customersUrl = "admin/customers/customer";

$condition = [
    'main_attributes' => [
        'status' => 'active',
    ]
];

print "Count of active customers\n";
$result = $api->api_call_head($customersUrl . '?' . http_build_query($condition));
print "Result: ";
if ($result) {
    print "Ok!\n";
    $count = isset($api->response_headers[SplynxApi::HEADER_X_TOTAL_COUNT]) ? $api->response_headers[SplynxApi::HEADER_X_TOTAL_COUNT] : 0;
    print "Count: " . var_export($count, true) . "\n";
} else {
    print "Fail! Error code: $api->response_code\n";
    print_r($api->response);
}
print "\n-------------------------------------------------\n";
