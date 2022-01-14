<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit;
}

class drop_download extends DownloadClass
{
    public function Download($link)
    {
        if (!preg_match('/^https:\/\/drop\.download\/(\w+)$/', $link)) {
            html_error('Invalid download link');
        }
        $ch = curl_init($link);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_COOKIEFILE     => 'droapk_cookie.txt',
            CURLOPT_COOKIEJAR      => 'droapk_cookie.txt',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36',
        ]);
        $response = curl_exec($ch);
        curl_setopt($ch, CURLOPT_URL, curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
        if (!preg_match('/<input type="hidden" name="op" value="(.*?)">/', $response, $op)) {
            html_error('Cannot get op.');
        }
        $op = $op[1];
        if (!preg_match('/<input type="hidden" name="usr_login" value="(.*?)">/', $response, $usr_login)) {
            html_error('Cannot get usr_login.');
        }
        $usr_login = $usr_login[1];
        if (!preg_match('/<input type="hidden" name="id" value="(.*?)">/', $response, $id)) {
            html_error('Cannot get id.');
        }
        $id = $id[1];
        if (!preg_match('/<input type="hidden" name="fname" value="(.*?)">/', $response, $fname)) {
            html_error('Cannot get fname.');
        }
        $fname = $fname[1];
        if (!preg_match('/<input type="hidden" name="referer" value="(.*?)">/', $response, $referer)) {
            html_error('Cannot get referer.');
        }
        $referer = $referer[1];
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        if (!preg_match('/<button type="submit" id="method_free" name="method_free" value="(.*?)"/', $response, $method_free)) {
            html_error('Cannot get method_free.');
        }
        $method_free = $method_free[1];
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['content-type: application/x-www-form-urlencoded']);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query(compact('op', 'usr_login', 'id', 'fname', 'referer', 'method_free'))
        );
        $response = curl_exec($ch);
        if (!preg_match('/<td align=right><div.*?><span style=\'position:absolute;padding-left:(\d+)px;padding-top:\d+px;\'>(.*?)<\/span><span style=\'position:absolute;padding-left:(\d+)px;padding-top:\d+px;\'>(.*?)<\/span><span style=\'position:absolute;padding-left:(\d+)px;padding-top:\d+px;\'>(.*?)<\/span><span style=\'position:absolute;padding-left:(\d+)px;padding-top:\d+px;\'>(.*?)<\/span>/', $response, $code)) {
            file_put_contents(__DIR__.'/page.txt', $response);
            html_error('Cannot get captcha.');
        }
        $digits = [
            $code[1] => html_entity_decode($code[2]),
            $code[3] => html_entity_decode($code[4]),
            $code[5] => html_entity_decode($code[6]),
            $code[7] => html_entity_decode($code[8]),
        ];
        if (count($digits) != 4) {
            html_error('Invalid captcha.');
        }
        ksort($digits, SORT_NUMERIC);
        $code = implode('', $digits);
        if (!preg_match('/<input type="hidden" name="op" value="(.*?)">/', $response, $op)) {
            html_error('Cannot get op.');
        }
        $op = $op[1];
        if (!preg_match('/<input type="hidden" name="id" value="(.*?)">/', $response, $id)) {
            html_error('Cannot get id.');
        }
        $id = $id[1];
        if (!preg_match('/<input type="hidden" name="rand" value="(.*?)">/', $response, $rand)) {
            html_error('Cannot get rand.');
        }
        $rand = $rand[1];
        if (!preg_match('/<input type="hidden" name="referer" value="(.*?)">/', $response, $referer)) {
            html_error('Cannot get referer.');
        }
        $referer = $referer[1];
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        if (!preg_match('/<input type="hidden" name="method_premium" value="(.*?)">/', $response, $method_premium)) {
            html_error('Cannot get method_premium.');
        }
        $method_premium = $method_premium[1];
        $adblock_detected = '0';
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query(compact('op', 'id', 'rand', 'referer', 'method_free', 'method_premium', 'adblock_detected', 'code'))
        );
        $response = curl_exec($ch);
        curl_close($ch);
        if (!preg_match('/<div class="download_box">[\s\n]+<a href="(.*?)"/', $response, $dl)) {
            html_error('Cannot get download link.');
        }
        $dl = $dl[1];

        return $this->RedirectDownload($dl, $fname);
    }
}

// [2022-01-14] Written by NimaH79
