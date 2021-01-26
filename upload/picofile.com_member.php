<?php

$upload_acc['picofile_com']['user'] = ''; // Set your username
$upload_acc['picofile_com']['pass'] = ''; // Set your password


if ($upload_acc['picofile_com']['user'] && $upload_acc['picofile_com']['pass']) {
    $_REQUEST['up_login'] = $upload_acc['picofile_com']['user'];
    $_REQUEST['up_pass'] = $upload_acc['picofile_com']['pass'];
    $_REQUEST['action'] = 'FORM';
    echo "<b><center>Using Default Login.</center></b>\n";
}

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
    echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
    <form method='POST'>
    <input type='hidden' name='action' value='FORM' />
    <tr><td style='white-space:nowrap;'>&nbsp;Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
    <tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
    echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
    echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
    echo "</table>\n</form>\n";
} else {
    echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>";
    if (empty($_REQUEST['up_login']) || empty($_REQUEST['up_pass'])) {
        html_error('Login failed: User/Password empty.');
    }
    if (!empty($_REQUEST['A_encrypted'])) {
        $_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
        $_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
        unset($_REQUEST['A_encrypted']);
    }
    $cookie_filepath = __DIR__ . '/picofile_cookie_'.md5(random_bytes(8)).'_'.time();
    $ch = curl_init('https://www.blogsky.com/login?service=picofile.com&returnurl=https://www.picofile.com/account/logon');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_COOKIEFILE => $cookie_filepath,
        CURLOPT_COOKIEJAR => $cookie_filepath
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    preg_match('/name="__RequestVerificationToken" type="hidden" value="(.*?)"/', $response, $token);
    $token = $token[1];
    $ch = curl_init('https://www.blogsky.com/login?service=picofile.com&returnurl=https://www.picofile.com/account/logon');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_COOKIEFILE => $cookie_filepath,
        CURLOPT_COOKIEJAR => $cookie_filepath,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            '__RequestVerificationToken' => $token,
            'UserName' => $_REQUEST['up_login'],
            'Password' => $_REQUEST['up_pass'],
            'Action' => 'ورود'
        ]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    if (!preg_match('/window\.parent\.location\.href = "(.*?)"/', $response, $href)) {
        deleteCookieFile($cookie_filepath);
        html_error('Couldn\'t login.');
    }
    $href = $href[1];
    $ch = curl_init($href);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_COOKIEFILE => $cookie_filepath,
        CURLOPT_COOKIEJAR => $cookie_filepath
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    preg_match('/uploadListFileID = (.*?);/', $response, $file_id);
    $file_id = $file_id[1];
    preg_match('/guid = "(.*?)"/', $response, $guid);
    $guid = $guid[1];
    preg_match('/uploadServers = "(.*?)"/', $response, $servers);
    $servers = explode(',', $servers[1]);
    $server = $servers[array_rand($servers)];
    $ch = curl_init('https://www.picofile.com/panel/contentlist/0');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_COOKIEFILE => $cookie_filepath,
        CURLOPT_COOKIEJAR => $cookie_filepath
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    preg_match_all('/<tr id="listnode\d+" nodeid="\d+" itemid=".*?" type="file" itemname="(.*?)" extension="(.*?)" size="\d+" server="(.*?)"/', $response, $files);
    $files_list = [];
    for ($i = 0; $i < count($files[0]); $i++) {
        $files_list[(function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower')($files[1][$i] . '.' . $files[2][$i])] = $files[3][$i];
    }
    if (array_key_exists((function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower')($lname), $files_list)) {
        $server = $files_list[(function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower')($lname)];
    }
    preg_match_all('/(\.(?:pf|bs)au)\t(\w+)/', file_get_contents($cookie_filepath), $cookies);
    $cookie_array = [];
    for ($i = 0; $i < count($cookies[0]); $i++) {
        $cookie_array[$cookies[1][$i]] = $cookies[2][$i];
    }
    $url = parse_url('https://'.$server.'.picofile.com/file/upload'.$guid.rand(0, 10000).'?uploadkey='.$guid.'_'.$file_id.'&username='.$_REQUEST['up_login']);
    $pfile = upfile($url['host'], 0, $url['path'].($url['query'] ? '?'.$url['query'] : ''), 0, $cookie_array, ['filename' => $lname], $lfile, $lname, 'fileupload', '', 0, 0, 0, $url['scheme']);
    is_page($pfile);
    $ch = curl_init('https://'.$server.'.picofile.com/file/fileuploadinfo'.$guid.rand(0, 10000).'?uploadkey='.$guid.'_'.$file_id.'&username='.$_REQUEST['up_login']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_COOKIEFILE => $cookie_filepath,
        CURLOPT_COOKIEJAR => $cookie_filepath
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, true);

    if (!isset($result['fileId'])) {
        deleteCookieFile($cookie_filepath);
        html_error('Upload failed!');
    }
    $download_link = 'http://'.$result['server'].'.picofile.com/file/'.$result['fileId'].'/'.preg_replace('/[^\x{00C0}-\x{FFFF}a-zA-Z0-9]+/ui', '_', $result['name']).'.'.$result['extension'].'.html';
    deleteCookieFile($cookie_filepath);
}

function deleteCookieFile($cookie_filepath)
{
    if (file_exists($cookie_filepath)) {
        unlink($cookie_filepath);
    }
}

// [26-1-2021]  Written by NimaH79.
