Youtube
=========

![Travis Youtube Build](https://api.travis-ci.org/alaouy/Youtube.svg?branch=master)

Laravel PHP Facade/Wrapper for the Youtube Data API v3 ( Non-OAuth )

You need to create an application and create your access token in the [developer console](https://console.developers.google.com).

## Installation

Add `alaouy/youtube` to your `composer.json`.
```
"alaouy/youtube": "dev-master"
```

Run `composer update` to pull down the latest version of the package.

Now open up `app/config/app.php` and add the service provider to your `providers` array.

```php
'providers' => array(
	'Alaouy\Youtube\YoutubeServiceProvider',
)
```

## Configuration
### For Laravel 5
Run `php artisan vendor:publish` and set your API key in the file:

```
/app/config/youtube.php
```

### For Laravel 4
Run `php artisan config:publish alaouy/youtube` and set your API key in the file:

```
/app/config/packages/alaouy/youtube/config.php
```

## Usage

```php
// Return an STD PHP object
$video = Youtube::getVideoInfo('rie-hPVJ7Sw');

// Get multiple videos info from an array
$videoList = Youtube::getVideoInfo(['rie-hPVJ7Sw','iKHTawgyKWQ']);

// Get multiple videos related to a video
$relatedVideos = Youtube::getRelatedVideos('iKHTawgyKWQ');

// Get popular videos in a country, return an array of PHP objects
$videoList = Youtube::getPopularVideos('us');

// Search playlists, channels and videos. return an array of PHP objects
$results = Youtube::search('Android');

// Only search videos, return an array of PHP objects
$videoList = Youtube::searchVideos('Android');

// Search only videos in a given channel, return an array of PHP objects
$videoList = Youtube::searchChannelVideos('keyword', 'UCk1SpWNzOs4MYmr0uICEntg', 40);

$results = Youtube::searchAdvanced(array( /* params */ ));

// Get channel data by channel name, return an STD PHP object
$channel = Youtube::getChannelByName('xdadevelopers');

// Get channel data by channel ID, return an STD PHP object
$channel = Youtube::getChannelById('UCk1SpWNzOs4MYmr0uICEntg');

// Get playlist by ID, return an STD PHP object
$playlist = Youtube::getPlaylistById('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Get playlist by channel ID, return an array of PHP objects
$playlists = Youtube::getPlaylistsByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Get items in a playlist by playlist ID, return an array of PHP objects
$playlistItems = Youtube::getPlaylistItemsByPlaylistId('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Get channel activities by channel ID, return an array of PHP objects
$activities = Youtube::getActivitiesByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Retrieve video ID from original YouTube URL
$videoId = Youtube::parseVidFromURL('https://www.youtube.com/watch?v=moSFlvxnbgk');
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
$search = Youtube::searchAdvanced($params, true);

// Check if we have a pageToken
if (isset($search['info']['nextPageToken'])) {
    $params['pageToken'] = $search['info']['nextPageToken'];
}

// Make another call and repeat
$search = Youtube::searchAdvanced($params, true);

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
$search = Youtube::paginateResults($params, null);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go to next page in result
$search = Youtube::paginateResults($params, $pageTokens[0]);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go to next page in result
$search = Youtube::paginateResults($params, $pageTokens[1]);

// Store token
$pageTokens[] = $search['info']['nextPageToken'];

// Go back a page
$search = Youtube::paginateResults($params, $pageTokens[0]);

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


## Youtube Data API v3
- [Youtube Data API v3 Doc](https://developers.google.com/youtube/v3/)
- [Obtain API key from Google API Console](https://console.developers.google.com)


##Credits
Built on code from Madcoda's [php-youtube-api](https://github.com/madcoda/php-youtube-api).
