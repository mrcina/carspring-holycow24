<?php
	class db
	{
		private static $_instance=null;
		public static $DB;
		
		/*
		public $db_host;
		public $db_user;
		public $db_pass;
		public $db_name;
		*/
		
		public $iid;
		public $__db;
		
		public $mysql_type = "mysqli";
		
		public static function getInstance($_arr = array()){
			if(!isset(self::$_instance) || self::$_instance===null){
				self::$_instance = new DB();
			}
			self::$DB = self::$_instance;
			return self::$_instance;
		}
		
		public function __construct(){
		}
		public static function set($var, $val){
			self::$_instance->$var = $val;
		}
		public static function _set($_arr = array()){
			if(is_array($_arr) && $_arr){
				foreach($_arr as $k => $v){
					self::$_instance->$k = $v;
				}
			}
		}
		public static function get($var, $default = NULL){
			if(!isset(self::$_instance->$var)){
				self::set($var, $default);
			}
			return self::$_instance->$var;
		}
		public static function config(){
			return main::toUrl("config");
		}
		public static function connect($try = 0){
			/*
			log::write(array(
				'where' => DIR_LOG."/db.log",
				'data' => 
					date(DATE_TIME_FORMAT, TIME_NOW)
					."\n".
					print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)
					.str_repeat('*', 33)."\n"
			));
			*/
			if(!function_exists('mysqli_init') && !extension_loaded('mysqli'))
				self::$DB->mysql_type = "mysql";
			else
				self::$DB->mysql_type = "mysqli";
			#var_dump(self::$DB->mysql_type); exit();
			if(self::$DB->mysql_type=="mysql"){
				$mysql = mysql_connect(
					self::$DB->db_host, 
					self::$DB->db_user, 
					self::$DB->db_pass
				);
				if(!$mysql){
					error_log('Could not connect: ' . mysql_error());
					if($try){
						return false;
					}
					return self::config();
				}
				$db_selected = mysql_select_db(self::$DB->db_name, $mysql);
				if(!$db_selected){
					error_log('Could not select DB '.mysql_error());
					if($try){
						return false;
					}
					return self::config();
				}
				mysql_query("SET character_set_client = 'utf8'"); 
				mysql_query("SET character_set_connection = 'utf8'"); 
				mysql_query("SET character_set_results = 'utf8'"); 
				mysql_query("SET character_set_server = 'utf8'");
				self::$DB->__db = $mysql;
				return $mysql;
			}else{
				if(!isset(self::$DB->db_host)){
					if($try){
						return false;
					}
					return self::config();
				}else{
					$mysql = new mysqli(
						self::$DB->db_host, 
						self::$DB->db_user, 
						self::$DB->db_pass,
						(isset(self::$DB->db_name)) ? self::$DB->db_name : null
					);
					if($mysql->connect_errno){
						error_log("Connect failed: %s\n". $mysql->connect_error);
						if($try){
							self::$DB->__db = $mysql;
							return $mysql->connect_errno;
						}
						return self::config();
					}
					$mysql->set_charset("utf8");
					
					self::$DB->__db = $mysql;
					return $mysql;
				}
			}
		}
		
		public static function _query($sql){
			$doquery = true;
			if(preg_match("/^update|^insert|^delete/i", trim($sql))){
				if(preg_match("/^insert/i", trim($sql))){
					$doquery = false;
					if(self::$DB->mysql_type=="mysqli")
						$rez = self::$DB->__db->query($sql) or die(self::$DB->__db->error);
					else
						$rez = mysql_query($sql) or die(mysql_error());
				}
			}
			if($doquery){
				#main::ppre(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)); exit();
				if(self::$DB->mysql_type=="mysqli"){
					if(self::get('log_query', false)){
						log::write(array(
							'where' => DIR_LOG."/db.log",
							'data' => 
								date(DATE_TIME_FORMAT, TIME_NOW)
								."\n"
								.$sql
								."\n"
								.str_repeat('*', 33)."\n"
						));
					}
					if(self::get('db_profiling', false)){
						self::$DB->__db->query('SET profiling=1');
					}
					if(DEBUG){
						#self::ppre(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
						$rez = self::$DB->__db->query($sql) or die(self::$DB->__db->error."\n\n".$sql);
					}else{
						$rez = self::$DB->__db->query($sql) or die(self::$DB->__db->error);
					}
					#self::set('iid', self::_insert_id(false));
					#self::$DB->iid = self::_insert_id(false);
					
					if(self::get('db_profiling', false)){
						if($result=self::$DB->__db->query("SHOW profiles", MYSQLI_USE_RESULT)){
							while($row=$result->fetch_row()){
								if(isset($row[1])){
									if($row[1]>1){
										self::ppre($row);
									}
								}
							}
							$result->close();
						}
						if($result=self::$DB->__db->query("SHOW profile FOR query 1", MYSQLI_USE_RESULT)){
							while($row=$result->fetch_row()){
								if(isset($row[1])){
									if($row[1]>1){
										self::ppre($row);
									}
								}
							}
							$result->close();
						}
						self::$DB->__db->query('SET profiling=0');
					}
					
				}else{
					$rez = mysql_query($sql) or die(mysql_error());
				}
			}
			return $rez;
		}
		public static function _query_log($sql){
			if(self::$DB->mysql_type=="mysqli")
				$rez = self::$DB->__db->query($sql) or die(self::$DB->__db->error);
			else
				$rez = mysql_query($sql) or die(mysql_error());
			return $rez;
		}
		public static function _store_result(){
			if(self::$mysql_type=="mysqli"){
				return mysqli_store_result(self::$DB->__db);
			}else{
			}
		}
		public static function _fetch_array($rez, $t = MYSQLI_NUM){
			if(self::$DB->mysql_type=="mysqli"){
				$row = $rez->fetch_array($t);
			}else{;
				$row = mysql_fetch_array($rez);
			}
			return $row;
		}
		public static function _fetch_assoc($rez){
			$row = array();
			if(self::$DB->mysql_type=="mysqli"){
				$row = $rez->fetch_assoc();
			}else{
				$row = mysql_fetch_assoc($rez);
			}
			return $row;
		}
		public static function _num_rows($rez){
			if(self::$DB->mysql_type=="mysqli")
				return $rez->num_rows;
			else
				return mysql_num_rows($rez);
		}
		public static function _insert_id($r = true){
			if($r){
				return self::$DB->iid;
			}
			if(self::$DB->mysql_type=="mysqli")
				return self::$DB->__db->insert_id;
			else
				return mysql_insert_id();
		}
		public static function _close(){
			if(self::$DB->mysql_type=="mysqli"){
				mysqli_close(self::$db);
			}else{
			}
		}
		public static function _real_escape_string($str){
			if(self::$DB->mysql_type=="mysqli")
				return self::$DB->__db->real_escape_string(trim($str));
			else
				return mysql_real_escape_string(trim($str));
		}
		#SANITIZE
		public static function clean($str){
			return preg_replace("/\\\"/", "\"", $str);
		}
		public static function get_fields($tbl, $no = array()){
			$_fields = array();
			$sql = "SHOW FULL COLUMNS FROM $tbl";
			$rez = self :: _query($sql);
			while($row=self :: _fetch_assoc($rez)){
				if(!in_array($row['Field'], $no))
					$_fields[] = $row['Field'];
			}
			$image = array_search('image', $_fields);
			if($image!==false){
				unset($_fields[$image]);
				$_fields[] = 'image';
			}
			return $_fields;
		}
		
		public static function get_all($tbl, $key = false, $activate = false, $sort = false, $count = false, $limit = -1, $check_lang = false, $prnt = false){
			$_tmp = array();
			$l = language::get('lext');
			$tbll = $tbl."$l";
			$sql = " select * from $tbl as t";
			if(!$check_lang){
				if($l!="" && !in_array($tbl, $this->_no_lng) && $key){
					$sql .= " inner join $tbll as l on t.$key = l.prnt";
				}
			}
			$sql .= " where 1 < 2 ";
			if($prnt){
				$_f = self::get_fields($tbl);
				if(in_array("prnt", $_f)){
					$sql .= " and prnt = '0' ";
				}
				#if(in_array("parent", $_f)){
					#$sql .= " and parent = '0' ";
				#}
			}
			#if($activate)
			#	$sql .= " and activate = '1' ";
			if($activate==-1){
				
			}elseif($activate){
				$_f = self::get_fields($tbl);
				if(in_array("activate", $_f)){
					if($activate==10){
						$sql .= " and t.activate = '0' ";
					}else{
						$sql .= " and t.activate = '1' ";
					}
				}
				if(in_array("is_active", $_f)){
					if($activate==0){
						$sql .= " and t.is_active = '0' ";
					}else{
						$sql .= " and t.is_active = '1' ";
					}
				}
				if(self::$main->all_del){
					$sql .= " and del = '0' ";
					self::$main->all_del = false;
				}
				#$sql .= " and del = '0' ";
			}
			
			if($sort)
				$sql .= " order by $sort asc";
			if(!$count){
				if($limit!=-1)
					$sql .= " LIMIT $limit, ".$this->PaginatePerPage;
			}
			#self::log_sql($sql);
			#print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
			$rez = DB :: _query($sql);
			if($count)
				return DB :: _num_rows($rez);
			while($row=DB :: _fetch_assoc($rez)){
				if($key)
					$_tmp["$row[$key]"] = $row;
				else
					$_tmp[] = $row; 
			}
			return $_tmp;
		}
		
		public static function get_by_id($tbl, $id = false, $custom = "id"){
			$sql = " select * from $tbl where $custom = '$id'";
			$rez = self::_query($sql);
			$row = self::_fetch_assoc($rez);
			return $row;
		}
		
		public static function sql_update($_arr, $t){
			$_tmp = array();
			$set = '';
			$_fields = self::get_fields($t);
			foreach($_arr as $pk => $pv){
				if(in_array($pk, $_fields)){
					#self::ppre(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true);
					if(!is_array($pv)){
						$set .= "`$pk` = '$pv', ";
					}
				}
			}
			$_tmp['set'] = rtrim($set, ', ');
			return $_tmp;
		}
		public static function sql_insert($_arr, $t, $_exclude){
			$_tmp = array();
			$_fields = self::array_unset(self::get_fields($t), $_exclude, 1);
			$f = '';
			$v = '';
			foreach($_arr as $pk => $pv){
				if(in_array($pk, $_fields)){
					$f .= "$pk, ";
					$v .= "'$pv', ";
				}
			}
			$f = rtrim($f, ', ');
			$v = rtrim($v, ', ');
			$_tmp['f'] = $f;
			$_tmp['v'] = $v;
			return $_tmp;
		}
		
		public static function array_unset($_what, $_which, $by_value = false, $_exclude = array()){
			foreach($_which as $k => $v){
				if($by_value){
					if(!in_array($v, $_exclude)){
						unset($_what[array_search($v, $_what)]);
					}
				}else{
					if(!in_array($v, $_exclude)){
						if(isset($_what[$v])){
							unset($_what[$v]);
						}
					}
				}
			}
			return $_what;
		}
		
		public static function ppre($_arr){
			echo "<pre>"; print_r($_arr); echo "</pre>";
		}
		
		public static function get_file_extension($file_name) {
			return substr(strrchr($file_name,'.'),1);
		}
		public static function xml2array($xmlObject, $out = array()){
			foreach((array) $xmlObject as $index => $node)
				$out[$index] = ( is_object ( $node ) ) ? self::xml2array ( $node ) : $node;
			return $out;
		}
		public static function implode_string($_arr){
			$tmp = "";
			foreach($_arr as $v){
				$tmp .= "\"$v\", ";
			}
			$tmp = trim($tmp, ", ");
			return $tmp;
		}
	}
?>