<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit();
}

class filescdn_net extends DownloadClass
{
    public function Download($link)
    {
        if (!preg_match('/filescdn\.net\/(\w+)/', $link, $link)) {
            html_error('Invalid download link');
        }
        $link = $link[1];
        $ch = curl_init('https://filescdn.net/'.$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'filescdn_cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'filescdn_cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $info = curl_exec($ch);
        if (!preg_match('/"rand" value="(.*?)"/', $info, $csrf)) {
            html_error('File not found.');
        }
        $csrf = $csrf[1];
        preg_match('/"op" value="(.*?)"/', $info, $op);
        $op = $op[1];
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['op' => $op, 'id' => $link, 'rand' => $csrf, 'referer' => '', 'method_free' => '', 'method_premium' => '']);
        $dl = curl_exec($ch);
        curl_close($ch);
        preg_match('/"(.*?)" o/u', $dl, $dl);
        $dl = $dl[1];

        return $this->RedirectDownload($dl, urldecode(basename(parse_url($dl, PHP_URL_PATH))));
    }
}

// Written by Th3-822.
// [2018-06-18] Updated by NimaH79
