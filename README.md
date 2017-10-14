YouTube
=========

![Travis YouTube Build](https://api.travis-ci.org/alaouy/Youtube.svg?branch=master)

Laravel PHP Facade/Wrapper for the YouTube Data API v3 ( Non-OAuth )

## Requirements

- PHP 7.0 or higher
- Laravel 5.1 or higher
- API key from [Google Console](https://console.developers.google.com)

Looking for YouTube Package for either of these: PHP 5, Laravel 5.0, Laravel 4? Visit the [`php5-branch`](https://github.com/alaouy/Youtube/tree/php5)

## Installation

Run in console below command to download package to your project:
```
composer require alaouy/youtube
```

## Configuration

In `/config/app.php` add YouTubeServiceProvider:
```
Alaouy\YouTube\YouTubeServiceProvider::class,
```

Do not forget to add also YouTube facade there:
```
'YouTube' => Alaouy\YouTube\Facades\YouTube::class,
```

Publish config settings:
```
$ php artisan vendor:publish --provider="Alaouy\YouTube\YouTubeServiceProvider"
```

Set your YouTube API key in the file:

```
/config/youtube.php
```


## Usage

```php
// use Alaouy\YouTube\Facades\YouTube;

// Return an STD PHP object
$video = YouTube::getVideoInfo('rie-hPVJ7Sw');

// Get multiple videos info from an array
$videoList = YouTube::getVideoInfo(['rie-hPVJ7Sw','iKHTawgyKWQ']);

// Get multiple videos related to a video
$relatedVideos = YouTube::getRelatedVideos('iKHTawgyKWQ');

// Get popular videos in a country, return an array of PHP objects
$videoList = YouTube::getPopularVideos('us');

// Search playlists, channels and videos. return an array of PHP objects
$results = YouTube::search('Android');

// Only search videos, return an array of PHP objects
$videoList = YouTube::searchVideos('Android');

// Search only videos in a given channel, return an array of PHP objects
$videoList = YouTube::searchChannelVideos('keyword', 'UCk1SpWNzOs4MYmr0uICEntg', 40);

// List videos in a given channel, return an array of PHP objects
$videoList = YouTube::listChannelVideos('UCk1SpWNzOs4MYmr0uICEntg', 40);

$results = YouTube::searchAdvanced(array( /* params */ ));

// Get channel data by channel name, return an STD PHP object
$channel = YouTube::getChannelByName('xdadevelopers');

// Get channel data by channel ID, return an STD PHP object
$channel = YouTube::getChannelById('UCk1SpWNzOs4MYmr0uICEntg');

// Get playlist by ID, return an STD PHP object
$playlist = YouTube::getPlaylistById('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Get playlist by channel ID, return an array of PHP objects
$playlists = YouTube::getPlaylistsByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Get items in a playlist by playlist ID, return an array of PHP objects
$playlistItems = YouTube::getPlaylistItemsByPlaylistId('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Get channel activities by channel ID, return an array of PHP objects
$activities = YouTube::getActivitiesByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Retrieve video ID from original YouTube URL
$videoId = YouTube::parseVidFromURL('https://www.youtube.com/watch?v=moSFlvxnbgk');
// result: moSFlvxnbgk
```

## Basic Search Pagination

```php
// Set default parameters
$params = array(
    'q'             => 'Android',
    'type'          => 'video',
    'part'          => 'id, snippet',
    'maxResults'    => 50
);

// Make intial call. with second argument to reveal page info such as page tokens
$search = YouTube::searchAdvanced($params, true);

// Check if we have a pageToken
if (isset($search['info']['nextPageToken'])) {
    $params['pageToken'] = $search['info']['nextPageToken'];
}

// Make another call and repeat
$search = YouTube::searchAdvanced($params, true);

// Add results key with info parameter set
print_r($search['results']);

/* Alternative approach with new built-in paginateResults function */

// Same params as before
$params = array(
    'q'             => 'Android',
    'type'          => 'video',
    'part'          => 'id, snippet',
    'maxResults'    => 50
);

// An array to store page tokens so we can go back and forth
$pageTokens = array();

// Make inital search
$search = YouTube::paginateResults($params, null);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go to next page in result
$search = YouTube::paginateResults($params, $pageTokens[0]);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go to next page in result
$search = YouTube::paginateResults($params, $pageTokens[1]);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go back a page
$search = YouTube::paginateResults($params, $pageTokens[0]);

// Add results key with info parameter set
print_r($search['results']);
```

The pagination above is quite basic. Depending on what you are trying to achieve you may want to create a recursive function that traverses the results.

## Run Unit Test
If you have PHPUnit installed in your environment, run:

```bash
$ phpunit
```

If you don't have PHPUnit installed, you can run the following:

```bash
$ composer update
$ ./vendor/bin/phpunit
```

## Format of returned data
The returned JSON is decoded as PHP objects (not Array).
Please read the ["Reference" section](https://developers.google.com/youtube/v3/docs/) of the Official API doc.


## YouTube Data API v3
- [YouTube Data API v3 Doc](https://developers.google.com/youtube/v3/)
- [Obtain API key from Google API Console](https://console.developers.google.com)


## Credits
Built on code from Madcoda's [php-youtube-api](https://github.com/madcoda/php-youtube-api).
