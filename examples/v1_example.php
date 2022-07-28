<?php
/**
 * Splynx API v1.0 demo script
 * Author: Ruslan Malymon (Splynx s.r.o.)
 * https://splynx.docs.apiary.io - API documentation
 */

include '../src/SplynxApi.php';

$api_url = 'http://splynx/'; // please set your Splynx URL

$key = "API_KEY"; // please set your key
$secret = "API_SECRET"; // please set your secret

// don't forget to add permissions to API Key, for changing locations.

$api = new SplynxApi($api_url, $key, $secret);

$locationsApiUrl = "admin/administration/locations";

print "<pre>";

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

print "Create location\n";
$result = $api->api_call_post($locationsApiUrl, [
    'name' => 'API test #' . rand()
]);

print "Result: ";
if ($result) {
    print "Ok!\n";
    print_r($api->response);
    $locationId = $api->response['id'];//@phpstan-ignore-line
} else {
    print "Fail! Error code: $api->response_code\n";
    print_r($api->response);
    $locationId = false;
}
print "\n-------------------------------------------------\n";

if ($locationId) {
    print "Retrieve location " . $locationId . "\n";
    $result = $api->api_call_get($locationsApiUrl, $locationId);
    print "Result: ";
    if ($result) {
        print "Ok!\n";
        print_r($api->response);
    } else {
        print "Fail! Error code: $api->response_code\n";
        print_r($api->response);
    }
    print "\n-------------------------------------------------\n";


    print "Change created location name\n";
    $result = $api->api_call_put($locationsApiUrl, $locationId, ['name' => 'NAME CHANGED #' . mt_rand()]);
    print "Result: ";
    if ($result) {
        print "Ok!\n";
        print_r($api->response);
    } else {
        print "Fail! Error code: $api->response_code\n";
        print_r($api->response);
    }
    print "\n-------------------------------------------------\n";

    print "Retrieve updated info\n";
    $result = $api->api_call_get($locationsApiUrl, $locationId);
    print "Result: ";
    if ($result) {
        print "Ok!\n";
        print_r($api->response);
    } else {
        print "Fail! Error code: $api->response_code\n";
        print_r($api->response);
    }
    print "\n-------------------------------------------------\n";

    print "Delete created location\n";
    $result = $api->api_call_delete($locationsApiUrl, $locationId);
    print "Result: ";
    if ($result) {
        print "Ok!\n";
        print_r($api->response);
    } else {
        print "Fail! Error code: $api->response_code\n";
        print_r($api->response);
    }
    print "\n-------------------------------------------------\n";
}
