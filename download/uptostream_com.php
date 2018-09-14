<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit();
}

class uptostream_com extends DownloadClass
{
    public function Download($link)
    {
        if (!preg_match('/http?s:\/\/(?:[\w\-]+\.)+[\w\-]+(?:\:\d+)?\/(\w{12})(?=(?:[\/\.]|(?:\.html?))?)/i', str_ireplace('/embed-', '/', $link))) {
            html_error('Invalid link?');
        }

        $page = $this->GetPage($link);
        is_present($page, '404 (File not found)', 'File Not Found');

        if (!preg_match('/var filename = \'(.*?)\'/', $page, $title)) {
            html_error('Error: Video title not found.');
        }
        $title = $title[1];

        if (!preg_match('/"src":"(.*?)"/', $page, $DL)) {
            html_error('Download link not found.');
        }
        $DL = $DL[1];
        $DL = str_replace('\\', '', $DL);

        $this->RedirectDownload($DL, $filename, 0, 0, 0, $filename);
    }
}

// [25-12-2015] Written by Th3-822.
// [28-08-2018] Updated by NimaH79.
