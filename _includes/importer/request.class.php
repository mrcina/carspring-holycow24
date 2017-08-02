<?php
	Class request{
		private static $__instance=null;
		public static $request;
		public function __construct($_arr = array()){
			
		}
		public static function getInstance($_arr = array()){
			if(!isset(self::$__instance) || self::$__instance===null){
				self::$__instance = new request($_arr);
			}
			self::$__instance = self::$__instance;
			return self::$__instance;
		}
		public static function set($var, $val){
			if(!isset(self::$__instance->$var)){
				self::$__instance->$var = null;
			}
			self::$__instance->$var = $val;
		}
		public static function get($var, $default = NULL){
			if(!isset(self::$__instance->$var)){
				self::set($var, $default);
			}
			return self::$__instance->$var;
		}
		public static function init($_conf = array()){
			if($_conf){
				foreach($_conf as $ck => $cv){
					self::set($ck, $cv);
				}
			}
			self::set("return_header", false);
			self::set("debug", false);
		}
		public static function request($_arr = array()){
			#echo "\n$url\n";
			$options = array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER         => false,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_ENCODING       => "",
					CURLOPT_USERAGENT      => "spider",
					CURLOPT_AUTOREFERER    => true,
					CURLOPT_CONNECTTIMEOUT => 120,
					CURLOPT_TIMEOUT        => 120,
					CURLOPT_MAXREDIRS      => 10,
					CURLOPT_SSL_VERIFYPEER => false
			);
			if(self::get('httpheader', false)){
				$_headers = array(
					'accept: image/webp,image/apng,image/*,*/*;q=0.8',
					'accept-encoding: gzip, deflate, br',
					'accept-language: en-US,en;q=0.8',
					'cache-control: no-cache',
					'cookie: __cfduid=d722f131b8727f802b0117ce1510f05311501429623; _gat_UA-60730723-1=1; _vwo_uuid_v2=F68AD0BB72141FDB2C6405E920623F1C|5513d43b1c7b737da134e0bae6f11723; _ga=GA1.3.2094785318.1501433547; _gid=GA1.3.795154273.1501433547',
					'pragma: no-cache',
					'referer: '.self::get('ref'),
					'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
				);
				$options[CURLOPT_HTTPHEADER] = $_headers;
			}
			$ch      = curl_init(self::get('url'));
			curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			#var_dump($header);
			curl_close( $ch );

			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = (self::get('sanitize', true)) ? self::sanitize($content, false) : $content;
			#var_dump($header);
			return $header;
		}
		public static function file_get_contents_curl($url, $decode_json = false) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);	
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			#curl_setopt($ch, CURLOPT_SSLVERSION, 5);
			#curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
			#var_dump(CURL_SSLVERSION_DEFAULT);
			#curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
			#curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0);
			#curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
			#curl_setopt($ch, CURLOPT_SSLVERSION, 6);
			if(self::get('httpheader', false)){
				$_headers = array(
					'accept: image/webp,image/apng,image/*,*/*;q=0.8',
					'accept-encoding: gzip, deflate, br',
					'accept-language: en-US,en;q=0.8',
					'cache-control: no-cache',
					'cookie: __cfduid=d722f131b8727f802b0117ce1510f05311501429623; _gat_UA-60730723-1=1; _vwo_uuid_v2=F68AD0BB72141FDB2C6405E920623F1C|5513d43b1c7b737da134e0bae6f11723; _ga=GA1.3.2094785318.1501433547; _gid=GA1.3.795154273.1501433547',
					'pragma: no-cache',
					'referer: '.self::get('ref'),
					'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
				);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
			}
			echo "<br/>$url<br/>";
			$data = curl_exec($ch);
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$_header  = curl_getinfo( $ch );
			#var_dump($err);
			#var_dump($errmsg);
			#db::ppre($_header);
			if(!in_array($_header['http_code'], array(
				200
			))){
				return false;
			}
			if($decode_json){
				if($decode_json==1){
					$data = json_decode($data);
				}
				if($decode_json==2){
					$data = json_decode($data, true);
				}
			}
			curl_close($ch);
			return $data;
		}
		public static function sanitize($buffer, $obgzh = true){
			// Remove comments
			$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
			// Remove space after colons
			$buffer = str_replace(': ', ':', $buffer);
			// Remove whitespace
			$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
			// Enable GZip encoding.
			if($obgzh){
				#var_dump(ob_list_handlers());
				if (!in_array('ob_gzhandler', ob_list_handlers())) {
					ob_start('ob_gzhandler');
				}else{
					ob_start();
				}
				#ob_start("ob_gzhandler");
				// Enable caching
				header('Cache-Control: public');
				// Expire in one day
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
				// Set the correct MIME type, because Apache won't set it for us
				header("Content-type: text/css");
			}
			return $buffer;
		}
	}
?>