<?php
	#echo getcwd();
	#exit();
	define("APPL_DIR", dirname(__FILE__));
	define("DEBUG", true);
	define("TMP", true);
	define("LIMIT", 500);
	#@ini_set("memory_limit","8M");
	@ini_set("pcre.backtrack_limit", "23001337");
	@ini_set("pcre.recursion_limit", "23001337");
	@ini_set('set_time_limit', 300);
	@ini_set('error_reporting', E_ALL);
	@ini_set('display_errors', 1);
	
	// Initialize db class
	require_once("db.class.php");
	db::getInstance();
	
	// Initialize request class
	require_once("request.class.php");
	request::getInstance();
	
	// Initialize cars class
	require_once("cars.class.php");
	cars::getInstance();
	cars::set('check_bad', 1);
	
	// Initialize reader class
	#require_once("reader.class.php");
	#reader::getInstance();
	
	// TMP constant for development environment
	if(!TMP){
		// GET xml from original source
		$url = "https://www.carspring.co.uk/rss-feed";
		request::set('url', $url);
		$data = request::request();
	
		// Saving to tmp file
		$file_tmp = "tmp/data-".(date("Y-m-d-H-i-s", time())).".xml";
		$fh = fopen($file_tmp, 'w');
		fwrite($fh, $data['content']);
		fclose($fh);
	}else{
		// Read .xml for testing
		$file_tmp = 'rss-feed.xml';
	}
	
	// Parse XML
	cars::fetch($file_tmp);
?>