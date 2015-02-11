Youtube
=========

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
Run `php artisan vendor:publish` and set your API key in the file :
```
/app/config/youtube.php
```
### For Laravel 4
Run `php artisan config:publish alaouy/youtube` and set your API key in the file :
```
/app/config/packages/alaouy/youtube/config.php
```

## Usage

```php

// Return a std PHP object 
$video = Youtube::getVideoInfo('rie-hPVJ7Sw');

// Get Multiple videos info from an array
$videoList = Youtube::getVideoInfo(['rie-hPVJ7Sw','iKHTawgyKWQ']);

// Get popular videos in a country, Return an array of PHP objects
$videoList = Youtube::getPopularVideos('us');

// Search playlists, channels and videos, Return an array of PHP objects
$results = Youtube::search('Android');

// Search only Videos, Return an array of PHP objects
$videoList = Youtube::searchVideos('Android');

// Search only Videos in a given channel, Return an array of PHP objects
$videoList = Youtube::searchChannelVideos('keyword', 'UCk1SpWNzOs4MYmr0uICEntg', 40);

$results = Youtube::searchAdvanced(array( /* params */ ));

// Return a std PHP object
$channel = Youtube::getChannelByName('xdadevelopers');

// Return a std PHP object
$channel = Youtube::getChannelById('UCk1SpWNzOs4MYmr0uICEntg');

// Return a std PHP object
$playlist = Youtube::getPlaylistById('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Return an array of PHP objects
$playlists = Youtube::getPlaylistsByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Return an array of PHP objects
$playlistItems = Youtube::getPlaylistItemsByPlaylistId('PL590L5WQmH8fJ54F369BLDSqIwcs-TCfs');

// Return an array of PHP objects
$activities = Youtube::getActivitiesByChannelId('UCk1SpWNzOs4MYmr0uICEntg');

// Parse Youtube URL into videoId
$videoId =Youtube::parseVIdFromURL('https://www.youtube.com/watch?v=moSFlvxnbgk');
// result: moSFlvxnbgk
```

## Basic Search Pagination
```php

// Set Default Parameters
$params = array(
    'q'             => 'Android',
    'type'          => 'video',
    'part'          => 'id, snippet',
    'maxResults'    => 50
);

// Make Intial Call. With second argument to reveal page info such as page tokens.
$search = Youtube::searchAdvanced($params, true);

// check if we have a pageToken
if (isset($search['info']['nextPageToken'])) {
    $params['pageToken'] = $search['info']['nextPageToken'];
}

// Make Another Call and Repeat
$search = Youtube::searchAdvanced($params, true);          

// add results key with info parameter set
print_r($search['results']); 

/* Alternative approach with new built in paginateResults function */
 
// Same Params as before
$params = array(
    'q'             => 'Android',
    'type'          => 'video',
    'part'          => 'id, snippet',
    'maxResults'    => 50
);

// an array to store page tokens so we can go back and forth
$pageTokens   = array();

// make inital search
$search       = Youtube::paginateResults($params, null);

// store token
$pageTokens[] = $search['info']['nextPageToken'];

// go to next page in result
$search       = Youtube::paginateResults($params, $pageTokens[0]);

// store token
$pageTokens[] = $search['info']['nextPageToken'];

// go to next page in result
$search       = Youtube::paginateResults($params, $pageTokens[1]);

// store token
$pageTokens[] = $search['info']['nextPageToken'];

// go back a page
$search       = Youtube::paginateResults($params, $pageTokens[0]);

// add results key with info parameter set
print_r($search['results']);

```

The pagination above is quite basic. Depending on what you are trying to achieve; you may want to create a recurssive function that traverses the results.


## Run Unit Test
If you have PHPUnit installed in your environment, just run

```bash
$ phpunit
```

If you don't have PHPUnit installed, you can run this

```bash
$ composer update
$ ./vendor/bin/phpunit
```

## Format of returned data
The returnd json is decoded as PHP objects (not Array).
Please read the ["Reference" section](https://developers.google.com/youtube/v3/docs/) of the Official API doc.


## Youtube Data API v3
- [Youtube Data API v3 Doc](https://developers.google.com/youtube/v3/)
- [Obtain API key from Google API Console](https://console.developers.google.com)


##Credits

Built on code from Madcoda's [php-youtube-api](https://github.com/madcoda/php-youtube-api).

