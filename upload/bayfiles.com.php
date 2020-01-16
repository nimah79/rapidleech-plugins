<?php

echo '<center>bayfiles.com plugin by <b>NimaH79</b></center><br>';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    html_error('You should have PHP 5.5 or higher.');
}

echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";

$url = parse_url('https://api.bayfiles.com/upload');
$pfile = upfile($url['host'], 0, $url['path'], 0, 0, 0, $lfile, $lname, 'file');
is_page($pfile);
preg_match('/({.*})/', $pfile, $pfile);
$pfile = $pfile[1];

$result = json_decode($pfile, true);

if (!isset($result['data']['file']['url']['full'])) {
    html_error('Upload failed!');
}

$download_link = $result['data']['file']['url']['full'];

// [28-08-2018] - Written by NiamH79
// [16-01-2020] - Added progress bar by NiamH79
