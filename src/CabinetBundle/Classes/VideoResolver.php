<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 20.12.16
 * Time: 15:29
 */

namespace CabinetBundle\Classes;


class VideoResolver
{
    const URL_PARSE_ERROR_MESSAGE = "Can't get information form this URL";

    public function getVideoData($url) {
        $url = $this->formatURL($url);
        if (false !== mb_stripos($url, 'youtube', null, 'utf-8')) return $this->getYoutubeData($url);

        return null;
    }

    private function getYoutubeData($url) {
        $parts = parse_url($url);
        parse_str($parts['query'], $params);
        if (!isset($params['v'])) throw new \Exception($this::URL_PARSE_ERROR_MESSAGE);
        $videoID = $params['v'];

        $fileInfo = [
            'url' => $url,
            'type' => 'video/youtube',
            'videoId' => $videoID
        ];

        $thumbCodes = [
            'maxresdefault.jpg',
            'hqdefault.jpg',
            '0.jpg',
            'mqdefault.jpg',
            'sddefault.jpg',
            'default.jpg'
        ];

        foreach ($thumbCodes as $i => $code) {
            $url = "http://img.youtube.com/vi/{$videoID}/{$code}";
            if (!isset($fileInfo['thumbUrl']) && $this->isResourseExists($url)) $fileInfo['thumbUrl'] = $url;
            if ($i>2 && !isset($fileInfo['thumbSmallUrl']) && $this->isResourseExists($url)) $fileInfo['thumbSmallUrl'] = $url;
        }

        return $fileInfo;
    }

    private function isResourseExists($url) {
        $headers = get_headers($url);
        return false !== mb_stripos($headers[0], '200 OK', null, 'utf-8');
    }

    private function formatURL($url) {
        return stripos($url, 'http://') === false ? "http://{$url}" : $url;
    }

}