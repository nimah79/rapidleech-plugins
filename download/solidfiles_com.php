<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit;
}

class solidfiles_com extends DownloadClass
{
    public function Download($link)
    {
        if (!preg_match('/solidfiles\.com\/v\/(\w+)/', $link, $link)) {
            html_error('Invalid download link');
        }
        $link = $link[1];
        $ch = curl_init('http://www.solidfiles.com/v/'.$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'solidfiles_cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'solidfiles_cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
        $csrf = curl_exec($ch);
        if (!preg_match('/\'csrfmiddlewaretoken\' value=\'(.*?)\'/', $csrf, $csrf)) {
            html_error('File not found.');
        }
        $csrf = $csrf[1];
        curl_setopt($ch, CURLOPT_URL, 'http://www.solidfiles.com/v/'.$link.'/dl');
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['csrfmiddlewaretoken' => $csrf, 'referrer' => '']);
        $dl = curl_exec($ch);
        curl_close($ch);
        preg_match('/"downloadUrl":"(.*?)"/u', $dl, $dl);
        $dl = $dl[1];

        return $this->RedirectDownload($dl, urldecode(basename(parse_url($dl, PHP_URL_PATH))));
    }
}

// Written by The Devil
// [2016-06-09] Code Clean Up
// [2017-04-16] Fixed Download Regex
// [2018-06-18] Fixed by NimaH79
