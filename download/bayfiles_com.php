<?php

if (!defined('RAPIDLEECH')) {
    require_once 'index.html';
    exit;
}

class bayfiles_com extends DownloadClass
{
    public function Download($link)
    {
        $page = $this->GetPage($link);
        if (!preg_match('/<a type="button" id="download-url" class="btn btn-primary btn-block" href="(.*?)">/', $page, $download_link)) {
            html_error('File not found!');
        }
        $this->RedirectDownload($download_link[1], 0, 0, 0, $link);
    }
}

// [16-08-2018] Written by NimaH79.
