<?php

echo '<center>gofile.io plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";

$ch = curl_init('https://apiv2.gofile.io/getServer');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server = curl_exec($ch);
curl_close($ch);
$server = json_decode($server, true);
if ($server['status'] != 'ok') {
    html_error('Can\'t get upload server.');
}
$url = parse_url('https://'.$server['data']['server'].'.gofile.io/upload');
$pfile = upfile($url['host'], 443, $url['path'], 0, 0, ['category' => 'file', 'comments' => '0'], $lfile, $lname, 'filesUploaded', '', 0, 0, 0, 'https');
is_page($pfile);
preg_match('/({.*})/', $pfile, $response);
$response = json_decode($response[1], true);
if ($response['status'] != 'ok') {
    html_error('Upload failed!');
}
$download_link = 'https://gofile.io/?c='.$response['data']['code'];

// [16-01-2020] - Written by NiamH79
