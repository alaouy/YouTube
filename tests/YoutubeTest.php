<?php

namespace Alaouy\Youtube\Tests;

use Alaouy\Youtube\Youtube;
use PHPUnit\Framework\TestCase;

class YoutubeTest extends TestCase
{
    /** @var Youtube */
    public $youtube;

    public function setUp()
    {
        $this->youtube = new Youtube(getenv("YOUTUBE_API_KEY"));
    }

    public function tearDown()
    {
        $this->youtube = null;
    }

    public function urlProvider()
    {
        return [
            ['https://'],
            ['http://www.yuotube.com'],
        ];
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructorFail()
    {
        $this->youtube = new Youtube(array());
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructorFail2()
    {
        $this->youtube = new Youtube('');
    }

    public function testSetApiKey()
    {
        $this->youtube->setApiKey('new_api_key');

        $this->assertEquals($this->youtube->getApiKey(), 'new_api_key');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidApiKey()
    {
        $this->youtube = new Youtube(array('key' => 'nonsense'));
        $vID = 'rie-hPVJ7Sw';
        $this->youtube->getVideoInfo($vID);
    }

    public function testGetCategories()
    {
        $region = 'US';
        $part = ['snippet'];
        $response = $this->youtube->getCategories($region,$part);

        $this->assertNotNull('response');
        $this->assertEquals('youtube#videoCategory', $response[0]->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('snippet', $response[0]);
    }

    public function testGetCommentThreadsByVideoId()
    {
        $videoId = 'rie-hPVJ7Sw';
        $response = $this->youtube->getCommentThreadsByVideoId($videoId);

        $this->assertNotNull('response');
        $this->assertEquals('youtube#commentThread', $response[0]->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('etag', $response[0]);
        $this->assertObjectHasAttribute('id', $response[0]);
        $this->assertObjectHasAttribute('snippet', $response[0]);
    }

    public function testGetVideoInfo()
    {
        $vID = 'rie-hPVJ7Sw';
        $response = $this->youtube->getVideoInfo($vID);

        $this->assertEquals($vID, $response->id);
        $this->assertNotNull('response');
        $this->assertEquals('youtube#video', $response->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('statistics', $response);
        $this->assertObjectHasAttribute('status', $response);
        $this->assertObjectHasAttribute('snippet', $response);
        $this->assertObjectHasAttribute('contentDetails', $response);
    }
    
    public function testGetLocalizedVideoInfo()
    {
        $videoId = 'vjF9GgrY9c0';
        $language = 'pl';

        $response = $this->youtube->getLocalizedVideoInfo($videoId, $language);

        $this->assertNotNull('response');
        $this->assertEquals('youtube#video', $response->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('statistics', $response);
        $this->assertObjectHasAttribute('status', $response);
        $this->assertObjectHasAttribute('snippet', $response);
        $this->assertObjectHasAttribute('contentDetails', $response);
    }

    public function testGetVideoInfoMultiple()
    {
        $vIDs = ['rie-hPVJ7Sw', 'iKHTawgyKWQ'];
        $response = $this->youtube->getVideoInfo($vIDs);

        $this->assertEquals($vIDs[0], $response[0]->id);
        $this->assertNotNull('response');
        $this->assertEquals('youtube#video', $response[0]->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('statistics', $response[0]);
        $this->assertObjectHasAttribute('status', $response[0]);
        $this->assertObjectHasAttribute('snippet', $response[0]);
        $this->assertObjectHasAttribute('contentDetails', $response[0]);
    }

    public function testGetPopularVideos()
    {
        $maxResult = rand(10, 30);
        $regionCode = 'us';
        $response = $this->youtube->getPopularVideos($regionCode, $maxResult);

        $this->assertNotNull('response');
        $this->assertEquals($maxResult, count($response));
        $this->assertEquals('youtube#video', $response[0]->kind);
        $this->assertObjectHasAttribute('statistics', $response[0]);
        $this->assertObjectHasAttribute('status', $response[0]);
        $this->assertObjectHasAttribute('snippet', $response[0]);
        $this->assertObjectHasAttribute('contentDetails', $response[0]);
    }

    public function testSearch()
    {
        $limit = rand(3, 10);
        $response = $this->youtube->search('Android', $limit);
        $this->assertEquals($limit, count($response));
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
    }

    public function testSearchVideos()
    {
        $limit = rand(3, 10);
        $response = $this->youtube->searchVideos('Android', $limit);
        $this->assertEquals($limit, count($response));
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
        $this->assertEquals('youtube#video', $response[0]->id->kind);
    }

    public function testSearchChannelVideos()
    {
        $limit = rand(3, 10);
        $response = $this->youtube->searchChannelVideos('Android', 'UCVHFbqXqoYvEWM1Ddxl0QDg', $limit);
        $this->assertEquals($limit, count($response));
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
        $this->assertEquals('youtube#video', $response[0]->id->kind);
    }

    public function testListChannelVideos()
    {
        $limit = rand(3, 10);
        $response = $this->youtube->listChannelVideos('UCVHFbqXqoYvEWM1Ddxl0QDg', $limit);
        $this->assertEquals($limit, count($response));
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
        $this->assertEquals('youtube#video', $response[0]->id->kind);
    }

    public function testSearchAdvanced()
    {
        //TODO
    }

    public function testGetRelatedVideos()
    {
        $limit = rand(3, 10);
        $vID = 'rie-hPVJ7Sw';
        $response = $this->youtube->getRelatedVideos($vID, $limit);

        $this->assertEquals($limit, count($response));
        $this->assertNotNull('response');
        $this->assertEquals('youtube#searchResult', $response[0]->kind);
        $this->assertEquals('youtube#video', $response[0]->id->kind);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('snippet', $response[0]);
    }

    public function testGetChannelByName()
    {
        $response = $this->youtube->getChannelByName('Google');

        $this->assertEquals('youtube#channel', $response->kind);
        //This is not a safe Assertion because the name can change, but include it anyway
        $this->assertEquals('Google', $response->snippet->title);
        //add all these assertions here in case the api is changed,
        //we can detect it instantly
        $this->assertObjectHasAttribute('snippet', $response);
        $this->assertObjectHasAttribute('contentDetails', $response);
        $this->assertObjectHasAttribute('statistics', $response);
    }

    public function testGetChannelById()
    {
        $channelId = 'UCk1SpWNzOs4MYmr0uICEntg';
        $response = $this->youtube->getChannelById($channelId);

        $this->assertEquals('youtube#channel', $response->kind);
        $this->assertEquals($channelId, $response->id);
        $this->assertObjectHasAttribute('snippet', $response);
        $this->assertObjectHasAttribute('contentDetails', $response);
        $this->assertObjectHasAttribute('statistics', $response);
    }

    public function testGetPlaylistsByChannelId()
    {
        $GOOGLE_CHANNELID = 'UCK8sQmJBp8GCxrOtXWBpyEA';
        $response = $this->youtube->getPlaylistsByChannelId($GOOGLE_CHANNELID);

        $this->assertTrue(count($response) > 0);
        $this->assertEquals('youtube#playlist', $response['results'][0]->kind);
        $this->assertEquals('Google', $response['results'][0]->snippet->channelTitle);
    }

    public function testGetPlaylistById()
    {
        //get one of the playlist
        $GOOGLE_CHANNELID = 'UCK8sQmJBp8GCxrOtXWBpyEA';
        $response = $this->youtube->getPlaylistsByChannelId($GOOGLE_CHANNELID);
        $playlist = $response['results'][0];

        $response = $this->youtube->getPlaylistById($playlist->id);
        $this->assertEquals('youtube#playlist', $response->kind);
    }

    public function testGetPlaylistByMultipleIds()
    {
        //get one of the playlist
        $GOOGLE_CHANNELID = 'UCK8sQmJBp8GCxrOtXWBpyEA';
        $response = $this->youtube->getPlaylistsByChannelId($GOOGLE_CHANNELID);
        $playlists = $response['results'];

        $response = $this->youtube->getPlaylistById([$playlists[0]->id, $playlists[1]->id]);
        $this->assertEquals('youtube#playlist', $response[0]->kind);
        $this->assertEquals('youtube#playlist', $response[1]->kind);
    }

    public function testGetPlaylistItemsByPlaylistId()
    {
        $GOOGLE_ZEITGEIST_PLAYLIST = 'PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs';
        $response = $this->youtube->getPlaylistItemsByPlaylistId($GOOGLE_ZEITGEIST_PLAYLIST);

        $data = $response['results'];
        $this->assertTrue(count($data) > 0);
        $this->assertEquals('youtube#playlistItem', $data[0]->kind);
    }

    public function testParseVIdFromURLFull()
    {
        $vId = $this->youtube->parseVidFromURL('http://www.youtube.com/watch?v=1FJHYqE0RDg');
        $this->assertEquals('1FJHYqE0RDg', $vId);
    }

    public function testParseVIdFromURLShort()
    {
        $vId = $this->youtube->parseVidFromURL('http://youtu.be/1FJHYqE0RDg');
        $this->assertEquals('1FJHYqE0RDg', $vId);
    }

    public function testParseVIdFromEmbedURL()
    {
        $vId = $this->youtube->parseVidFromURL('http://youtube.com/embed/1FJHYqE0RDg');
        $this->assertEquals('1FJHYqE0RDg', $vId);
    }

    /**
     * @dataProvider urlProvider
     * @expectedException \Exception
     */
    public function testParseVIdFromURLException($url)
    {
        $vId = $this->youtube->parseVidFromURL($url);
    }

    /**
     * @expectedException \Exception
     */
    public function testParseVIdException()
    {
        $vId = $this->youtube->parseVidFromURL('http://www.facebook.com');
    }

    public function testGetActivitiesByChannelId()
    {
        $GOOGLE_CHANNELID = 'UCK8sQmJBp8GCxrOtXWBpyEA';
        $response = $this->youtube->getActivitiesByChannelId($GOOGLE_CHANNELID);
        $this->assertTrue(count($response) > 0);
        $this->assertEquals('youtube#activity', $response[0]->kind);
        $this->assertEquals('Google', $response[0]->snippet->channelTitle);
    }

    /**
     *
     *
     * @expectedException  \InvalidArgumentException
     */
    public function testGetActivitiesByChannelIdException()
    {
        $channelId = '';
        $response = $this->youtube->getActivitiesByChannelId($channelId);
    }

    public function testGetChannelFromURL()
    {
        $channel = $this->youtube->getChannelFromURL('http://www.youtube.com/user/Google');

        $this->assertEquals('UCK8sQmJBp8GCxrOtXWBpyEA', $channel->id);
        $this->assertEquals('Google', $channel->snippet->title);
    }

    /**
     * Test skipped for now, since the API returns Error 500
     */
    public function testNotFoundAPICall()
    {
        $vID = 'Utn7NBtbHL4'; //an deleted video
        $response = $this->youtube->getVideoInfo($vID);
        $this->assertFalse($response);
    }

    /**
     * Test skipped for now, since the API returns Error 500
     * @expectedException \Exception
     */
    public function testNotFoundAPICall2()
    {
        $channelId = 'non_exist_channelid';
        $response = $this->youtube->getPlaylistsByChannelId($channelId);
        $this->assertEquals($response->getStatusCode(), 404);
    }
}
