<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit;
}

class fex_net extends DownloadClass
{
    public function Download($link)
    {
        if (!preg_match('/\/s\/(.*)$/', $link, $slug)) {
            html_error('Invalid URL');
        }
        $slug = $slug[1];
        $token = $this->GetPage('https://api.fex.net/api/v1/anonymous/upload-token');
        if (!preg_match('/"token":"(.*?)"/', $token, $token)) {
            html_error('Cannot get token');
        }
        $token = $token[1];
        $id = $this->GetPage('https://api.fex.net/api/v2/file/share/'.$slug);
        if (!preg_match('/"id":(\d+),"is_dir/', $id, $id)) {
            html_error('Cannot get ID');
        }
        $id = $id[1];
        $info = $this->GetPage('https://api.fex.net/api/v2/file/share/children/'.$slug.'/'.$id);
        if (!preg_match('/"download_url":"(.*?)"/', $info, $dlink)) {
            html_error('File not found');
        }
        $dlink = $dlink[1];
        preg_match('/"name":"(.*?)",/', $info, $FileName);
        $FileName = json_decode('"'.$FileName[1].'"');
        $cookie = 'token='.$token;
        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $link);
    }
}

// [17-09-2020] Written by NimaH79.
