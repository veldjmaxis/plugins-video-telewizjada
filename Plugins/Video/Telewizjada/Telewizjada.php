<?php
/**
 * Copyright (c) 2016. veldjmaxis <djmaxis@op.pl>
 */

namespace djmaxis\Plugins\Video\Telewizjada;

/**
 * This class is written for objective use of code written by UNKNOWN.
 * I made some changes, fixes and code refactor.
 *
 * Class Telewizjada
 * @package djmaxis\plugins\video\telewizjada
 */
class Telewizjada
{

    /**
     * @var string
     */
    private $epgURL = "http://epg.iptvlive.org";

    /**
     * Url to our script
     * @var string
     */
    private $webUrl;

    /**
     * m3u string
     * @var string
     */
    private $m3uString = "#EXTM3U\r\n";

    /**
     * Switch for better look while sending output to browser
     * @var bool
     */
    private $htmlOutputFormat = false;

    /**
     * Filter channels
     * @var bool
     */
    private $onlineOnly = false;

    /**
     * @var bool
     */
    private $adultVisible = false;

    /**
     * Telewizjada constructor.
     * @param string $url Url to main sctipt file
     */
    public function __construct($url)
    {
        try {
            // Url to our main script file
            $this->webUrl = filter_var($url, FILTER_VALIDATE_URL);

            if (!$this->webUrl) {
                throw new \HttpUrlException('Script URL is missing or wrong URL!');
            }
        } catch (\HttpUrlException $e) {
            //It would be better to send this error to Yours app error handler
            error_log($e);
        }
    }

    /**
     * Sends curl request
     * @param string $url Webpage url
     * @param string|null $postOptions post content
     * @param bool $sendHeader include header
     * @return mixed
     */
    public function getUrl($url, $postOptions = null, $sendHeader = false)
    {

        //Code below is written by ... ??? WHO ??
        //regardless of this, I greet U
        //I made only minor refactoring
        $curl = curl_init($url);
        //Include HOST into header to force crossdomain policy
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Host: www.telewizjada.net'));
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, $sendHeader);
        curl_setopt($curl, CURLOPT_REFERER, 'http://releases.flowplayer.org/6.0.3/commercial/flowplayerhls.swf');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        if ($postOptions !== null) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postOptions);
        }

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**
     * Returns chunk list of selected channel
     * @param int $cid Channel ID
     * @return mixed
     */
    public function getChannel($cid = 1)
    {
        try {
            if (!$cid) {
                throw new \HttpInvalidParamException('CID must be an integer value!');
            }
            //Yeah, that below is written by UNKNOWN too
            //I made only minor changes
            $uri = json_decode($this->getUrl('http://www.telewizjada.net/get_mainchannel.php', "cid=$cid"), true);

            if (!is_array($uri)) {
                throw new \UnexpectedValueException('Failed to get main channel!');
            }

            $this->getUrl('http://www.telewizjada.net/set_cookie.php', "url={$uri['url']}", true);
            $channelUrl = json_decode($this->getUrl('http://www.telewizjada.net/get_channel_url.php', "cid=$cid"), true);

            if (!is_array($channelUrl)) {
                throw new \UnexpectedValueException('Failed to get channel url!');
            }

            //Additional code containing URL fix
            //We need to extract original URL to playlist, for later merge in chunklist
            $urlForChunks = explode('playlist.m3u8', $channelUrl['url'])[0];

            //We need to replace 'chunklist' with full url path to chunklist
            return str_replace('chunklist', $urlForChunks . 'chunklist', $this->getUrl($channelUrl['url']));

        } catch (\Exception $e) {
            error_log($e);
            return null;
        }
    }

    /**
     * Returns channels list
     * @return string
     */
    public function getChannelsList()
    {
        try {
            $links = json_decode($this->getUrl('http://www.telewizjada.net/get_channels.php'), true);

            if (!is_array($links)) {
                throw new \UnexpectedValueException('Failed to get channels!');
            }

            foreach ($links['channels'] as $link) {
                //Skip offline or adult channels if onlineOnly is true or if adultVisible is false
                if (($this->onlineOnly && !$link['online']) || ($link['isAdult'] && !$this->adultVisible)) {
                    continue;
                }

                $this->m3uString .= sprintf("#EXTINF:-1 tvg-id=\"%s\" group-title=\"Telewizjada\" tvg-logo=\"http://telewizjada.net%s\",%s\r\n%s?cid=%s\r\n\r\n",
                    $link['displayName'],
                    $link['thumb'],
                    $link['displayName'],
                    $this->webUrl,
                    $link['id']);
            }

            if ($this->htmlOutputFormat) {
                $this->m3uString = nl2br($this->m3uString);
            }

            return $this->m3uString;

        } catch (\Exception $e) {
            error_log($e);
            return null;
        }
    }

    /**
     * This function is just a redirect to epg service - for lazy people ;)
     * @return mixed
     */
    public function getEPG()
    {
        return header("Location: $this->epgURL");
    }

    /**
     * Sets html output format
     * @param bool $format
     * @return $this
     */
    public function setHtmlOutputFormat($format)
    {
        $this->htmlOutputFormat = $format;

        return $this;
    }

    /**
     * @param $onlineOnly
     * @return $this
     */
    public function setOnlineOnly($onlineOnly)
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    /**
     * @param boolean $adultVisible
     * @return $this
     */
    public function setAdultVisible($adultVisible)
    {
        $this->adultVisible = $adultVisible;

        return $this;
    }
}