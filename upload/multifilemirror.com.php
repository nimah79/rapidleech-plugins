<?php

echo '<center>multifilemirror.com plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";

$ch = curl_init('https://multifilemirror.com/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);
$response = curl_exec($ch);
curl_close($ch);
preg_match('/action="(.*?)"/', $response, $action);
$action = $action[1];
preg_match('/upload_id=(.*)/', $action, $upload_id);
$upload_id = $upload_id[1];
$url = parse_url($action);
$pfile = upfile($url['host'], 0, $url['path'].($url['query'] ? '?'.$url['query'] : ''), 0, 0, [
    'u_hash'                  => '',
    'remote'                  => '0',
    'upload_id'               => $upload_id,
    'fileuploader-list-file0' => json_encode([['file' => '0:/'.$lname]]),
    'filepass'                => '',
    'tabs'                    => 'on',
], $lfile, $lname, 'file0');
is_page($pfile);
preg_match('/({.*})/', $pfile, $response);
$response = json_decode($response[1], true);
$ch = curl_init('https://multifilemirror.com/result/'.$response['code']);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);
$response = curl_exec($ch);
curl_close($ch);
preg_match('/<textarea id="fullurl".*?>(.*?)&#13;&#10;</', $response, $download_link);
$download_link = str_replace(' ', '%20', $download_link[1]);

// [16-01-2020] - Written by NiamH79
