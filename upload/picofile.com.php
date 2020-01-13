<?php

echo '<center>picofile.com plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";
$ch = curl_init('http://www.picofile.com');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
]);
$main = curl_exec($ch);
preg_match('/uploadListFileID = (.*?);/', $main, $file_id);
$file_id = $file_id[1];
preg_match('/guid = "(.*?)"/', $main, $guid);
$guid = $guid[1];
preg_match('/uploadServers = "(.*?)"/', $main, $server);
$server = explode(',', $server[1])[0];
$url = parse_url('http://' . $server . '.picofile.com/file/upload' . $guid . '9211?uploadkey=' . $guid . '_' . $file_id);
$pfile = upfile($url['host'], 0, $url['path'].($url["query"] ? "?" . $url["query"] : ""), 0, 0, ['filename' => $lname], $lfile, $lname, 'fileupload');
is_page($pfile);
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://' . $server . '.picofile.com/file/fileuploadinfo' . $guid . '4104?uploadkey=' . $guid . '_' . $file_id
]);
$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result, true);

if(!isset($result['fileId'])) {
    html_error('Upload failed!');
}

$download_link = 'http://' . $result['server'] . '.picofile.com/file/' . $result['fileId'] . '/' . $result['name'] . '.' . $result['extension'] . '.html';

// [14-01-2020] - Written by NiamH79
