<?php

namespace GutenPress\Helpers;

class Urls{
	public static function getCurrent( $include_query = false, $include_port = false ){
		// Protocol
		$url = 'http';
		if ( !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) $url .= "s"; // https
		$url .= "://";

		// Server Name
		$url .= $_SERVER["HTTP_HOST"];
		if ( $include_port && $_SERVER["SERVER_PORT"] != "80" ) $url .= ':'.$_SERVER["SERVER_PORT"];

		// Path
		$path = parse_url($_SERVER["REQUEST_URI"]);
		$url .= $path['path'];
		if( $include_query && !empty($path['query']) ) $url .= '?' . $path['query'];

		// URL
		return $url;
	}
}