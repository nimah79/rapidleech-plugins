<?php

echo '<center>bayfiles.com plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

$ch = curl_init('https://bayfiles.com/api/upload');
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($lfile)]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result, true);

if (!isset($result['data']['file']['url']['full'])) {
    html_error('Upload failed!');
}

$download_link = $result['data']['file']['url']['full'];

// [28-08-2018] - Written by NiamH79
