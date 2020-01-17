<?php

echo "<center>file.io plugin by <b>NimaH79</b></center><br><table style='width:600px;margin:auto;'>\n<tr><td align='center'>";
$url = parse_url('https://file.io/?expires=99999999999y');
$pfile = upfile($url['host'], 0, $url['path'].($url['query'] ? '?'.$url['query'] : ''), 0, 0, 0, $lfile, $lname, 'file');
is_page($pfile);
preg_match('/({.*})/', $pfile, $response);
$response = json_decode($response[1], true);
if (!$response['success']) {
    html_error('Upload failed!');
}
$download_link = $response['link'];

// [18-01-2020] - Written by NiamH79
