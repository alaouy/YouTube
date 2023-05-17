<?php

namespace Alaouy\Youtube;

class Youtube
{

    /**
     * @var string
     */
    protected $youtube_key; // from the config file

    /**
     * @var array
     */
    public $APIs = [
        'categories.list' => 'https://www.googleapis.com/youtube/v3/videoCategories',
        'videos.list' => 'https://www.googleapis.com/youtube/v3/videos',
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
        'channels.list' => 'https://www.googleapis.com/youtube/v3/channels',
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists',
        'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',
        'activities' => 'https://www.googleapis.com/youtube/v3/activities',
        'commentThreads.list' => 'https://www.googleapis.com/youtube/v3/commentThreads',
    ];

    /**
     * @var array
     */
    public $youtube_reserved_urls = [
        '\/about\b',
        '\/account\b',
        '\/account_(.*)',
        '\/ads\b',
        '\/creators\b',
        '\/feed\b',
        '\/feed\/(.*)',
        '\/gaming\b',
        '\/gaming\/(.*)',
        '\/howyoutubeworks\b',
        '\/howyoutubeworks\/(.*)',
        '\/new\b',
        '\/playlist\b',
        '\/playlist\/(.*)',
        '\/reporthistory',
        '\/results\b',
        '\/shorts\b',
        '\/shorts\/(.*)',
        '\/t\/(.*)',
        '\/upload\b',
        '\/yt\/(.*)',
    ];

    /**
     * @var array
     */
    public $page_info = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Constructor
     * $youtube = new Youtube(['key' => 'KEY HERE'])
     *
     * @param string $key
     * @throws \Exception
     */
    public function __construct($key, $config = [])
    {
        if (is_string($key) && !empty($key)) {
            $this->youtube_key = $key;
        } else {
            throw new \Exception('Google API key is Required, please visit https://console.developers.google.com/');
        }
        $this->config['use-http-host'] = isset($config['use-http-host']) ? $config['use-http-host'] : false;
    }

    /**
     * @param $setting
     * @return Youtube
     */
    public function useHttpHost($setting)
    {
        $this->config['use-http-host'] = !!$setting;

        return $this;
    }

    /**
     * @param $key
     * @return Youtube
     */
    public function setApiKey($key)
    {
        $this->youtube_key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->youtube_key;
    }

    /**
     * @param $regionCode
     * @return \StdClass
     * @throws \Exception
     */
    public function getCategories($regionCode = 'US', $part = ['snippet'])
    {
        $API_URL = $this->getApi('categories.list');
        $params = [
            'key' => $this->youtube_key,
            'part' => implode(',', $part),
            'regionCode' => $regionCode
        ];

        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeMultiple($apiData);
    }

    /**
     * @param string $videoId       Instructs the API to return comment threads containing comments about the specified channel. (The response will not include comments left on videos that the channel uploaded.)
     * @param integer $maxResults   Specifies the maximum number of items that should be returned in the result set. Acceptable values are 1 to 100, inclusive. The default value is 20.
     * @param string $order         Specifies the order in which the API response should list comment threads. Valid values are: time, relevance.
     * @param array $part           Specifies a list of one or more commentThread resource properties that the API response will include.
     * @param bool $pageInfo        Add page info to returned array.
     * @param string $pageToken     The pageToken parameter identifies a specific page in the result set that should be returned.
     * @return array
     * @throws \Exception
     */
    public function getCommentThreadsByVideoId($videoId = null, $maxResults = 20, $order = null, $part = ['id', 'replies', 'snippet'], $pageInfo = false, $pageToken = null) {

        return $this->getCommentThreads(null, null, $videoId, $maxResults, $order, $part, $pageInfo, $pageToken);
    }

    /**
     * @param string $channelId     Instructs the API to return comment threads containing comments about the specified channel. (The response will not include comments left on videos that the channel uploaded.)
     * @param string $id            Specifies a comma-separated list of comment thread IDs for the resources that should be retrieved.
     * @param string $videoId       Instructs the API to return comment threads containing comments about the specified channel. (The response will not include comments left on videos that the channel uploaded.)
     * @param integer $maxResults   Specifies the maximum number of items that should be returned in the result set. Acceptable values are 1 to 100, inclusive. The default value is 20.
     * @param string $order         Specifies the order in which the API response should list comment threads. Valid values are: time, relevance.
     * @param array $part           Specifies a list of one or more commentThread resource properties that the API response will include.
     * @param bool $pageInfo        Add page info to returned array.
     * @param string $pageToken     The pageToken parameter identifies a specific page in the result set that should be returned.     
     * @return array
     * @throws \Exception
     */
    public function getCommentThreads($channelId = null, $id = null, $videoId = null, $maxResults = 20, $order = null, $part = ['id', 'replies', 'snippet'], $pageInfo = false, $pageToken = null)
    {
        $API_URL = $this->getApi('commentThreads.list');

        $params = array_filter([
            'channelId' => $channelId,
            'id' => $id,
            'videoId' => $videoId,
            'maxResults' => $maxResults,
            'part' => implode(',', $part),
            'order' => $order,
	    'pageToken' => $pageToken,
        ]);

        $apiData = $this->api_get($API_URL, $params);

        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info' => $this->page_info,
            ];
        } else {
            return $this->decodeList($apiData);
        }
    }

    /**
     * @param $vId
     * @param array $part
     * @return \StdClass
     * @throws \Exception
     */
    public function getVideoInfo($vId, $part = ['id', 'snippet', 'contentDetails', 'player', 'statistics', 'status'])
    {
        $API_URL = $this->getApi('videos.list');
        $params = [
            'id' => is_array($vId) ? implode(',', $vId) : $vId,
            'key' => $this->youtube_key,
            'part' => implode(',', $part),
        ];

        $apiData = $this->api_get($API_URL, $params);

        if (is_array($vId)) {
            return $this->decodeMultiple($apiData);
        }

        return $this->decodeSingle($apiData);
    }

    /**
     * Gets localized video info by language (f.ex. de) by adding this parameter after video id
     * Youtube::getLocalizedVideoInfo($video->url, 'de')
     *
     * @param $vId
     * @param $language
     * @param array $part
     * @return \StdClass
     * @throws \Exception
     */

    public function getLocalizedVideoInfo($vId, $language, $part = ['id', 'snippet', 'contentDetails', 'player', 'statistics', 'status']) {

        $API_URL = $this->getApi('videos.list');
        $params = [
            'id'    => is_array($vId) ? implode(',', $vId) : $vId,
            'key' => $this->youtube_key,
            'hl'    =>  $language,
            'part' => implode(',', $part),
        ];

        $apiData = $this->api_get($API_URL, $params);

        if (is_array($vId)) {
            return $this->decodeMultiple($apiData);
        }

        return $this->decodeSingle($apiData);
    }

    /**
     * Gets popular videos for a specific region (ISO 3166-1 alpha-2)
     *
     * @param $regionCode
     * @param integer $maxResults
     * @param array $part
     * @return array
     */
    public function getPopularVideos($regionCode, $maxResults = 10, $part = ['id', 'snippet', 'contentDetails', 'player', 'statistics', 'status'])
    {
        $API_URL = $this->getApi('videos.list');
        $params = [
            'chart' => 'mostPopular',
            'part' => implode(',', $part),
            'regionCode' => $regionCode,
            'maxResults' => $maxResults,
        ];

        $apiData = $this->api_get($API_URL, $params);

        return $this->decodeList($apiData);
    }

    /**
     * Simple search interface, this search all stuffs
     * and order by relevance
     *
     * @param $q
     * @param integer $maxResults
     * @param array $part
     * @return array
     */
    public function search($q, $maxResults = 10, $part = ['id', 'snippet'])
    {
        $params = [
            'q' => $q,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos
     *
     * @param  string $q Query
     * @param  integer $maxResults number of results to return
     * @param  string $order Order by
     * @param  array $part
     * @return \StdClass  API results
     */
    public function searchVideos($q, $maxResults = 10, $order = null, $part = ['id'])
    {
        $params = [
            'q' => $q,
            'type' => 'video',
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos in the channel
     *
     * @param  string $q
     * @param  string $channelId
     * @param  integer $maxResults
     * @param  string $order
     * @param  array $part
     * @param  $pageInfo
     * @return array
     */
    public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = null, $part = ['id', 'snippet'], $pageInfo = false)
    {
        $params = [
            'q' => $q,
            'type' => 'video',
            'channelId' => $channelId,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params, $pageInfo);
    }

    /**
     * List videos in the channel
     *
     * @param  string $channelId
     * @param  integer $maxResults
     * @param  string $order
     * @param  array $part
     * @param  $pageInfo
     * @return array
     */
    public function listChannelVideos($channelId, $maxResults = 10, $order = null, $part = ['id', 'snippet'], $pageInfo = false)
    {
        $params = [
            'type' => 'video',
            'channelId' => $channelId,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params, $pageInfo);
    }

    /**
     * Generic Search interface, use any parameters specified in
     * the API reference
     *
     * @param $params
     * @param $pageInfo
     * @return array
     * @throws \Exception
     */
    public function searchAdvanced($params, $pageInfo = false)
    {
        $API_URL = $this->getApi('search.list');

        if (empty($params) || (!isset($params['q']) && !isset($params['channelId']) && !isset($params['videoCategoryId']))) {
            throw new \InvalidArgumentException('at least the Search query or Channel ID or videoCategoryId must be supplied');
        }

        $apiData = $this->api_get($API_URL, $params);
        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info' => $this->page_info,
            ];
        } else {
            return $this->decodeList($apiData);
        }
    }

    /**
     * Generic Search Paginator, use any parameters specified in
     * the API reference and pass through nextPageToken as $token if set.
     *
     * @param $params
     * @param $token
     * @return array
     */
    public function paginateResults($params, $token = null)
    {
        if (!is_null($token)) {
            $params['pageToken'] = $token;
        }

        if (!empty($params)) {
            return $this->searchAdvanced($params, true);
        }
    }

    /**
     * @param $username
     * @param array $optionalParams
     * @param array $part
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelByName($username, $optionalParams = [], $part = ['id', 'snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('channels.list');
        $params = [
            'forUsername' => $username,
            'part' => implode(',', $part),
        ];

        $params = array_merge($params, $optionalParams);

        $apiData = $this->api_get($API_URL, $params);

        return $this->decodeSingle($apiData);
    }

	/**
	 * @param $username
	 * @param $maxResults
	 * @param $part
	 * @return false|\StdClass
	 * @throws \Exception
	 */
	public function searchChannelByName($username, $maxResults = 1, $part = ['id', 'snippet'])
	{
		$params = [
			'q' => $username,
			'part' => implode(',', $part),
			'type' => 'channel',
			'maxResults' => $maxResults,
		];

		$search = $this->searchAdvanced($params);

		if (!empty($search[0]->snippet->channelId)) {
			$channelId = $search[0]->snippet->channelId;
			return $this->getChannelById($channelId);
		}
	}

    /**
     * @param $id
     * @param array $optionalParams
     * @param array $part
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelById($id, $optionalParams = [], $part = ['id', 'snippet', 'contentDetails', 'statistics'])
    {
        $API_URL = $this->getApi('channels.list');
        $params = [
            'id' => is_array($id) ? implode(',', $id) : $id,
            'part' => implode(',', $part),
        ];

        $params = array_merge($params, $optionalParams);

        $apiData = $this->api_get($API_URL, $params);

        if (is_array($id)) {
            return $this->decodeMultiple($apiData);
        }

        return $this->decodeSingle($apiData);
    }

    /**
     * @param string $channelId
     * @param array $optionalParams
     * @param array $part
     * @return array
     * @throws \Exception
     */
    public function getPlaylistsByChannelId($channelId, $optionalParams = [], $part = ['id', 'snippet', 'status'])
    {
        $API_URL = $this->getApi('playlists.list');
        $params = [
            'channelId' => $channelId,
            'part' => implode(',', $part)
        ];

        $params = array_merge($params, $optionalParams);

        $apiData = $this->api_get($API_URL, $params);

        $result = ['results' => $this->decodeList($apiData)];
        $result['info']['totalResults'] =  (isset($this->page_info['totalResults']) ? $this->page_info['totalResults'] : 0);
        $result['info']['nextPageToken'] = (isset($this->page_info['nextPageToken']) ? $this->page_info['nextPageToken'] : false);
        $result['info']['prevPageToken'] = (isset($this->page_info['prevPageToken']) ? $this->page_info['prevPageToken'] : false);

        return $result;
    }

    /**
     * @param $id
     * @param $part
     * @return \StdClass
     * @throws \Exception
     */
    public function getPlaylistById($id, $part = ['id', 'snippet', 'status'])
    {
        $API_URL = $this->getApi('playlists.list');
        $params = [
            'id' => is_array($id)? implode(',', $id) : $id,
            'part' => implode(',', $part),
        ];
        $apiData = $this->api_get($API_URL, $params);

        if (is_array($id)) {
            return $this->decodeMultiple($apiData);
        }

        return $this->decodeSingle($apiData);
    }

    /**
     * @param string $playlistId
     * @param string $pageToken
     * @param integer $maxResults
     * @param array $part
     * @return array
     * @throws \Exception
     */
    public function getPlaylistItemsByPlaylistId($playlistId, $pageToken = '', $maxResults = 50, $part = ['id', 'snippet', 'contentDetails', 'status'])
    {
        $API_URL = $this->getApi('playlistItems.list');
        $params = [
            'playlistId' => $playlistId,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];

        // Pass page token if it is given, an empty string won't change the api response
        $params['pageToken'] = $pageToken;

        $apiData = $this->api_get($API_URL, $params);
        $result = ['results' => $this->decodeList($apiData)];
        $result['info']['totalResults'] =  (isset($this->page_info['totalResults']) ? $this->page_info['totalResults'] : 0);
        $result['info']['nextPageToken'] = (isset($this->page_info['nextPageToken']) ? $this->page_info['nextPageToken'] : false);
        $result['info']['prevPageToken'] = (isset($this->page_info['prevPageToken']) ? $this->page_info['prevPageToken'] : false);

        return $result;
    }

    /**
     * @param $channelId
     * @param array $part
     * @param integer $maxResults
     * @param $pageInfo
     * @param $pageToken
     * @return array
     * @throws \Exception
     */
    public function getActivitiesByChannelId($channelId, $part = ['id', 'snippet', 'contentDetails'], $maxResults = 5, $pageInfo = false, $pageToken = '')
    {
        if (empty($channelId)) {
            throw new \InvalidArgumentException('ChannelId must be supplied');
        }
        $API_URL = $this->getApi('activities');
        $params = [
            'channelId' => $channelId,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
            'pageToken' => $pageToken,
        ];
        $apiData = $this->api_get($API_URL, $params);

        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info' => $this->page_info,
            ];
        } else {
            return $this->decodeList($apiData);
        }
    }

    /**
     * @param  string $videoId
     * @param  integer $maxResults
     * @param  array $part
     * @return array
     * @throws \Exception
     */
    public function getRelatedVideos($videoId, $maxResults = 5, $part = ['id', 'snippet'])
    {
        if (empty($videoId)) {
            throw new \InvalidArgumentException('A video id must be supplied');
        }
        $API_URL = $this->getApi('search.list');
        $params = [
            'type' => 'video',
            'relatedToVideoId' => $videoId,
            'part' => implode(',', $part),
            'maxResults' => $maxResults,
        ];
        $apiData = $this->api_get($API_URL, $params);

        return $this->decodeList($apiData);
    }

    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be)
     *
     * @param  string $youtube_url
     * @throws \Exception
     * @return string Video Id
     */
    public static function parseVidFromURL($youtube_url)
    {
        if (strpos($youtube_url, 'youtube.com')) {
            if (strpos($youtube_url, 'embed')) {
                $path = static::_parse_url_path($youtube_url);
                $vid = substr($path, 7);
                return $vid;
            } else {
                $params = static::_parse_url_query($youtube_url);
                return $params['v'];
            }
        } else if (strpos($youtube_url, 'youtu.be')) {
            $path = static::_parse_url_path($youtube_url);
            $vid = substr($path, 1);
            return $vid;
        } else {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }
    }

    /**
     * Get the channel object by supplying the URL of the channel page
     *
     * @param  string $youtube_url
     * @throws \Exception
     * @return object Channel object
     */
    public function getChannelFromURL($youtube_url)
    {
        if (strpos($youtube_url, 'youtube.com') === false) {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }

        $path = static::_parse_url_path($youtube_url);
        $segments = explode('/', $path);

        if (strpos($path, '/channel/') === 0) {
            $channelId = $segments[count($segments) - 1];
            $channel = $this->getChannelById($channelId);
        } else if (strpos($path, '/user/') === 0) {
            $username = $segments[count($segments) - 1];
            $channel = $this->getChannelByName($username);
        } else if (strpos($path, '/c/') === 0) {
            $username = $segments[count($segments) - 1];
            $channel = $this->searchChannelByName($username);
        } else if (strpos($path, '/@') === 0) {
            $username = str_replace('@', '', $segments[count($segments) - 1]);
            $channel = $this->searchChannelByName($username);
        } else {
            foreach ($this->youtube_reserved_urls as $r) {
                if (preg_match('/'.$r.'/', $path)) {
                    throw new \Exception('The supplied URL does not look like a Youtube Channel URL');
                }
            }

	        $username = $segments[1];
	        $channel = $this->searchChannelByName($username);
        }

        return $channel;
    }

    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */

    /**
     * @param $name
     * @return mixed
     */
    public function getApi($name)
    {
        return $this->APIs[$name];
    }

    /**
     * Decode the response from youtube, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param  string $apiData the api response from youtube
     * @throws \Exception
     * @return \StdClass  an Youtube resource object
     */
    public function decodeSingle(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        } else {
            if(isset($resObj->items)){
                $itemsArray = $resObj->items;
                if (!is_array($itemsArray) || count($itemsArray) == 0) {
                    return false;
                } else {
                    return $itemsArray[0];
                }
            }
           return false;
        }
    }

    /**
     * Decode the response from youtube, extract the multiple resource object.
     *
     * @param  string $apiData the api response from youtube
     * @throws \Exception
     * @return \StdClass  an Youtube resource object
     */
    public function decodeMultiple(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        } else {
            
            if(isset($resObj->items)) {
                $itemsArray = $resObj->items;
                if (!is_array($itemsArray) || count($itemsArray) == 0) {
                    return false;
                } else {
                    return $itemsArray;
                }
            }
            return false;
        }
    }

    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param  string $apiData response string from youtube
     * @throws \Exception
     * @return array Array of StdClass objects
     */
    public function decodeList(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }

            throw new \Exception($msg);
        } else {
            $this->page_info = [
                'kind' => $resObj->kind,
                'etag' => $resObj->etag,
                'prevPageToken' => null,
                'nextPageToken' => null,
            ];

            if (isset($resObj->pageInfo)) {
                $this->page_info['resultsPerPage'] = $resObj->pageInfo->resultsPerPage;
                $this->page_info['totalResults'] = $resObj->pageInfo->totalResults;
            }

            if (isset($resObj->prevPageToken)) {
                $this->page_info['prevPageToken'] = $resObj->prevPageToken;
            }

            if (isset($resObj->nextPageToken)) {
                $this->page_info['nextPageToken'] = $resObj->nextPageToken;
            }

            if(isset($resObj->items)) {
                $itemsArray = $resObj->items;
                if (!is_array($itemsArray) || count($itemsArray) == 0) {
                    return false;
                } else {
                    return $itemsArray;
                }
            }
            return false;
        }
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function api_get($url, $params)
    {
        //set the youtube key
        $params['key'] = $this->youtube_key;

        //boilerplates for CURL
        $tuCurl = curl_init();

        if (isset($_SERVER['HTTP_HOST']) && $this->config['use-http-host']) {
            curl_setopt($tuCurl, CURLOPT_HEADER, array('Referer' => $_SERVER['HTTP_HOST']));
        }

        curl_setopt($tuCurl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params));
        if (strpos($url, 'https') === false) {
            curl_setopt($tuCurl, CURLOPT_PORT, 80);
        } else {
            curl_setopt($tuCurl, CURLOPT_PORT, 443);
        }

        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new \Exception('Curl Error : ' . curl_error($tuCurl));
        }

        return $tuData;
    }

    /**
     * Parse the input url string and return just the path part
     *
     * @param  string $url the URL
     * @return string      the path string
     */
    public static function _parse_url_path($url)
    {
        $array = parse_url($url);

        return $array['path'];
    }

    /**
     * Parse the input url string and return an array of query params
     *
     * @param  string $url the URL
     * @return array      array of query params
     */
    public static function _parse_url_query($url)
    {
        $array = parse_url($url);
        $query = $array['query'];

        $queryParts = explode('&', $query);

        $params = [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = empty($item[1]) ? '' : $item[1];
        }

        return $params;
    }
}
