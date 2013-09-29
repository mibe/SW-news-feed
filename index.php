<?php

// Config
$CACHE_FILE = 'feed.xml';
$CACHE_TIME = 60 * 60 * 24;				// 1 day

header('Content-Type: application/rss+xml');

$time = time();
$filetime = filemtime($CACHE_FILE);

// If we have a cache hit, output the cache file and exit
if ($filetime !== FALSE && $time < $filetime + $CACHE_TIME)
{
	readfile($CACHE_FILE);
	exit;
}

// Cache miss, so generate the feed

require_once('FeedWriter/Feed.php');
require_once('FeedWriter/RSS2.php');
require_once('FeedWriter/Item.php');
require_once('Scraper.php');

use \FeedWriter\RSS2;

$scraper = new Scraper();
$entries = $scraper->getAllPages();

$feed = new RSS2;

$feed->setTitle('Schweinfurt - Bürgerinformationen');
$feed->setLink('http://www.schweinfurt.de/buergerinformationen/index.html');

foreach($entries as $entry)
{
	$item = $feed->createNewItem();
	$item->setTitle($entry->title);
	$item->setLink($entry->url);
	$item->setDescription($entry->message);

	$feed->addItem($item);
}

$feedContent = $feed->generateFeed();

file_put_contents($CACHE_FILE, $feedContent);

print $feedContent;
