<?php

echo '<center>trainbit.com plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";

$url = parse_url('https://trainbit-upload.parsaspace.com/upload');
$pfile = upfile($url['host'], 443, $url['path'], 0, 0, ['token' => ''], $lfile, $lname, 'file', '', 0, 0, 0, $url['scheme']);
is_page($pfile);
preg_match('/({.*})/', $pfile, $response);
$response = json_decode($response[1], true);
if ($response['Result'] != 'success') {
    html_error('Upload failed!');
}
$ch = curl_init('https://parsaspace.com/upload/getlink');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['fileid' => $response['FileId']]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json']
]);
$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result, true);
$download_link = 'https://trainbit.com/files/'.$result['fileid'].'/';

// [16-01-2020] - Written by NiamH79
