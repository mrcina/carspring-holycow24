<?php
	Class cars{
		private static $__instance=null;
		public static $cars;
		public function __construct($_arr = array()){
		}
		public static function getInstance($_arr = array()){
			if(!isset(self::$__instance) || self::$__instance===null){
				self::$__instance = new cars();
			}
			self::$cars = self::$__instance;
			return self::$__instance;
		}
		public static function set($var, $val){
			self::$__instance->$var = $val;
		}
		public static function _set($_arr = array()){
			if(is_array($_arr) && $_arr){
				foreach($_arr as $k => $v){
					self::$__instance->$k = $v;
				}
			}
		}
		public static function get($var, $default = NULL){
			if(!isset(self::$__instance->$var)){
				self::set($var, $default);
			}
			return self::$__instance->$var;
		}
		public static function insert($_arr){
			#$_tmp = db::sql_insert($_arr, self::get('table', 'cars'), array('id'));
			#$sql = "INSERT IGNORE INTO ".self::get('table', 'cars')." ($_tmp[f]) VALUES ($_tmp[v])";
			$_tmp = db::sql_update($_arr, self::get('table', 'cars'));
			$sql = " INSERT IGNORE INTO ".self::get('table', 'cars')." SET $_tmp[set] ";
			#exit($sql);
			$rez = db::_query($sql);
			#$_arr['id'] = $iid = db::get('iid');
			$_arr['id'] = $iid = db::_insert_id(false);
			#var_dump($iid);
			return $_arr;
		}
		public static function fetch($file_tmp){
			$xml = simplexml_load_file($file_tmp) or die("Error: Cannot create object");
			$_rez = array();
			$_main_images = array();
			$i=0;
			foreach($xml->children() as $car){
				// Convert xmlSimplObject to array
				$car = db::xml2array($car);
				
				if(!$car['financeprovider']){
					$car['financeprovider'] = '';
				}
				
				// Saving data to array
				$_rez[$i] = $car;
				
				$i++;
				if(LIMIT && $i==LIMIT){
					break;
				}
			}
			#db::ppre($_rez); exit();
			if($_rez){
				db::_set(array(
					'db_host' => 'localhost',
					#'db_user' => 'dbo692544141',
					'db_user' => 'holycow24',
					#'db_pass' => 'deOopapSoIPY',
					'db_pass' => '9xHWjtQHZHnKBDXD',
					#'db_name' => 'db692544141',
					'db_name' => 'carspring_holycow24'
				));
				db::connect();
				
				
				#$sql = "TRUNCATE cars";
				#db::_query($sql);
				
				db::_query("UPDATE cars SET is_image_ok_try = '0' WHERE is_image_ok_try >= '3'");
				
				// Store data to cars table
				$i=0;
				foreach($_rez as $_r){
					$r['status'] = 1;
					$_car = self::insert($_r);
					#print_r($_car);
					if($_car['id']){
						// Prepare images for download; only unique SKU (that does not exists based on INSERT IGNORE...
						$_main_images[$i] = array(
							'sku' => $_r['sku'],
							'mainimage' => $_r['mainimage'],
							'url' => $_r['url']
						);
						$i++;
					}
				}
			}
			self::proceed($_rez, $_main_images);
		}
		public static function proceed($_rez, $_main_images){
			// Some dumps
			#db::ppre($_rez);
			#db::ppre($_main_images);
			$_bad = array();
			if(!defined("LIMIT_PROCEED")){
				#define("LIMIT_PROCEED", 3);
			}
			if($_main_images){
				request::set('httpheader', true);
				$i=0;
				foreach($_main_images as $_img){
					// Retreive image data with curl
					request::set('url', $_img['mainimage']);
					request::set('ref', $_img['url']);
					#request::set('url', urlencode($_img['mainimage']));
					#$_data = request::request();
					echo "$_img[sku]<br/>";
					#$content = request::file_get_contents_curl($_img['mainimage']);
					
					$filename = "$_img[sku].".db::get_file_extension($_img['mainimage']);
					$filenameOut = "sources/$filename";
					
					#$output = shell_exec("wget --user-agent=\"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092416 Firefox/3.0.3\" \"$_img[mainimage]\" -O \"$filename\"");
					#echo getcwd();
					#exit();
					
					// Shell download
					echo "<br/><br/>";
					$cmd = "wget --user-agent=\"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092416 Firefox/3.0.3\" \"$_img[mainimage]\" -O \"$filenameOut\" 2>&1";
					echo "$cmd<br/>";
					$output = shell_exec($cmd);
					var_dump($output);
					echo "<br/><br/>";
					#exit();
					
					$content = false;
					if(strpos($output, "200 OK")!==false){
						$content = true;
					}
					
					if(!$content){
						$_bad[] = $_img['sku'];
						if(isset($_img['is_image_ok_try']) && $_img['is_image_ok_try']){
							db::_query("UPDATE cars SET is_image_ok_try = '$_img[is_image_ok_try]' WHERE sku = '$_img[sku]'");
						}
						sleep(5);
					}else{
						#$_data['content'] = $content;
					
						///*
						// Retreive image data alternative
						#$_data['content'] = file_get_contents($_img['mainimage']);
						
						/*
						$arrContextOptions = array(
							"ssl" => array(
								"verify_peer" => false,
								"verify_peer_name" => false,
							)
						);
						$_data['content'] = file_get_contents($_img['mainimage'], false, stream_context_create($arrContextOptions));
						//*/
						
						/*
						// Create image filename -> sku.extension
						$filename = "$_img[sku].".db::get_file_extension($_img['mainimage']);
						// Image location
						$filenameOut = "sources/$filename";
						
						// Saving image to sources
						$fh = fopen($filenameOut, 'w');
						fwrite($fh, $_data['content']);
						fclose($fh);
						*/
						
						// Copy image to inbox dir
						@copy($filenameOut, "retouching/inbox/$filename");
						
						sleep(1);
						
						db::_query("UPDATE cars SET is_image_ok = '1', is_image_ok_try = '4' WHERE sku = '$_img[sku]'");
						//*/
					}
					$i++;
					if(defined("LIMIT_PROCEED") && LIMIT_PROCEED && $i==LIMIT_PROCEED){
						break;
					}
				}
				if($_bad){
					#db::ppre($_bad);
					foreach($_bad as $sku){
						#db::_query("DELETE FROM cars WHERE sku = '$sku'");
						$skus = db::implode_string($_bad);
						#db::_query("UPDATE cars SET is_image_ok = '1', is_image_ok_try = '4' WHERE sku NOT IN ($skus)");
					}
				}
			}
			if(self::get('check_bad', false)){
				self::bad();
			}
		}
		public static function bad(){
			$_rez = array();
			$_main_images = array();
			$sql = "SELECT * FROM cars WHERE is_image_ok = '0' AND is_image_ok_try < '3' ";
			#$sql = "SELECT * FROM cars WHERE is_image_ok = '0' ";
			$rez = db::_query($sql);
			while($row=db::_fetch_assoc($rez)){
				$is_image_ok_try = $row['is_image_ok_try']+1;
				$_rez[] = $row;
				$_main_images[] = array(
					'sku' => $row['sku'],
					'mainimage' => $row['mainimage'],
					'url' => $row['url'],
					'is_image_ok_try' => $is_image_ok_try
				);
			}
			if($_rez && $_main_images){
				self::proceed($_rez, $_main_images);
			}
		}
	}
?>