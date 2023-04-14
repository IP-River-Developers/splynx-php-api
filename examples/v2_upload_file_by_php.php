<?php // phpcs:ignoreFile
/**
 * Splynx API v2.0 demo script
 * Author: Roman Muzichuk (Splynx s.r.o.)
 * https://splynx.docs.apiary.io - API documentation
 */

$files = [
    './path/file1.txt',
    './path/file2.png',
];

$domainName = "YOUR_SPLYNX_DOMAIN";
$apiKey = 'YOUR_API_KEY';
$apiSecret = 'YOUR_API_SECRET';
$ticketMessageId = 'TICKET_MESSAGE_ID';

$nonce = round(microtime(true) * 100);
$signature = strtoupper(hash_hmac('sha256', $nonce . $apiKey, $apiSecret));

$authData = array(
    'key' => $apiKey,
    'signature' => $signature,
    'nonce' => $nonce++
);
$authString = http_build_query($authData);
$header = 'Authorization: Splynx-EA (' . $authString . ')';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $domainName . '/api/2.0/admin/support/ticket-attachments?message_id=' . $ticketMessageId);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$postData = [];
foreach ($files as $index => $file) {
    $postData['files[' . $index . ']'] = new CURLFile($file);
}
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    $header,
    'Content-Type: multipart/form-data',
));

$response = curl_exec($ch);
$response = json_decode($response, true);

print_r($response);
curl_close($ch);
