<?php

namespace bulk_converter;

const VOD_DIR = '/usr/local/WowzaMediaServer/content/vod';


function glob_r($dir, $pattern = '*')
{
	$items = glob("$dir/$pattern");
	foreach (glob("$dir/*", GLOB_ONLYDIR) as $d)
		$items = array_merge($items, glob_r($d, $pattern));
	return $items;
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<', '?xml version="1.0" encoding="UTF-8"?', '>', "\n";
echo '<videos>', "\n";
foreach (array('mp4', 'flv') as $format) {
	foreach (glob_r(VOD_DIR, "*.$format") as $path) {
		$name = ltrim(substr($path, strlen(VOD_DIR)), '/');
		echo '<video>', htmlspecialchars($name), '</video>', "\n";
	}
}
echo '</videos>';
