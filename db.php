<?php
/*
*title:						db.php
*author:					Daniel K. Valente
*created:					10-04-2021
*modified:					02-08-2022
*license:					Apache-2.0
*ver:						0.0.0.2
*desc:						Provides a PHP class that allows for an easier database management system that automatically establishes and manages SQL stream connections and executes SQL queries based on the PHP array/object submitted to it.
*dev:						false
*patch-notes:				
0.0.0.2:
	- Added the ability to generate a unique randomized string for true unique column values.
	- Added the ability to generate a randomized string of x length.
	- Added a NOT operator.
0.0.0.1:
	- Added custom SQL execution.
	- Added software/system/api update functionality/method (Can be called via db::checkUpdates();).
	- Added the ability to set the database username, password, server, and port if a config file is not specified.
	- Added a method to check if record exists within the database.
	- Added the ability to conduct a backup of the database.
*/
/*
										USAGE
I.) Requirements:
	- A configuration file to be specified.
	- Configuration file must be in the form of an ini file.
II.) Configuring:
	- You can specify the following...
		server=
		username=
		password=
		port=
		backup_dir=
*/
if (!class_exists("db")) {
class db {
	// DO NOT MODIFIED LINE BELOW
	protected static $ver="0.0.0.2";
	// CONFIG:
	private static $config_file=false;
	private static $backup_dir=false;
	// Public accessible properties:
	public static $db=false;
	public static $tb=false;
	public static $cmd=false;
	public static $action=false;
	public static $cols=false;
	public static $dataTypes=false;
	public static $values=false;
	public static $status=false;
	public static $args=false;
	public static $res=array();
	public static $persistent=false;
	// Private properties:
	private static $con=false;
	private static $username="root";
	private static $password="";
	private static $server="localhost";
	private static $port=3306;
	// Class constants:
	private static $PHP_DATA_TYPES=array(
		"string",
		"integer",
		"double",
		"float",
		"boolean",
		"array",
		"object",
		"null",
		"NULL",
		"resource"
	);
	public static $help=false;
	// Error handling...
	private static function error($q=0) {
		$res="An unknown error has occurred.";
		if ($q===0) {
			$res="An unknown error occurred.";
		} else if ($q===1) {
			$res="Unable to download update... Server responded with an error...";
		} else if ($q===2) {
			$res="Server responded with an error.";
		}
		$res="ERROR: ".$res;
		var_dump($res);
	}
	// Initialization process.
	public static function ini($q=false) {
		//global $__db;
		$__db=array();
		if (self::$config_file===false) {
			if (gettype($q)==="string") {
				if (file_exists($q)) {
					if (is_file($q)) {
						if (strtolower(pathinfo($q)["extension"])==="ini") {
							$__db=parse_ini_file($q);
						}
					}
				}
			}
		}
		if (isset($__db)) {
			if (isset($__db["server"])) {
				self::$server=$__db["server"];
				db::$server=$__db["server"];
			}
			if (isset($__db["username"])) {
				self::$username=$__db["username"];
				db::$username=$__db["username"];
			}
			if (isset($__db["password"])) {
				self::$password=$__db["password"];
				db::$password=$__db["password"];
			}
			if (isset($__db["port"])) {
				self::$port=$__db["port"];
				db::$port=$__db["port"];
			}
			if (isset($__db["backup_dir"])) {
				self::$backup_dir=$__db["backup_dir"];
				db::$backup_dir=$__db["backup_dir"];
			}
		} else {
			//var_dump($__db);
		}
		self::$help=self::getHelp();
		return self::$help;
	}
	// Returns an HTML string consisting of the help information.
	public static function getHelp() {
		//$p=preg_replace("/(\\\\)/","/",__DIR__)."/db_help.html";
		$p=dirname(self::$config_file)."/db_help.html";
		$res=false;
		$f=false;
		if (file_exists($p)) {
			if (is_file($p)) {
				if (strtolower(pathinfo($p)["extension"])==="html") {
					if (self::$help===false) {
						//$res=file_get_contents($p);
						if (filesize($p)>0) {
							$f=fopen($p,"r");
							$res=fread($f,filesize($p));
							fclose($f);
							if (strstr($res,"\\\"")) {
								$res=preg_replace("/\\\\\"/","\"",$res);
							}
							//$res=preg_replace("/([\\\\]*\$)/","",$res);
							if (strstr($res,"$")) {
								//var_dump(preg_match("/([\\\\]*\$)/",$res));
								$res=preg_replace("/([\\\\]*\$)/","",$res);
							}
						} else {
							$res="No Help Found";
						}
					} else {
						$res=self::$help;
					}
				}
			}
		}
		unset($p,$f);
		return $res;
	}
	// Checks for updates on this class object.
	public static function checkUpdates() {
		$q=connection_status();
		$obj=false;
		$data=false;
		if ($q!==CONNECTION_ABORTED) {
			$p=curl_init();
			curl_setopt($p,CURLOPT_URL,"http://doft.ddns.net/software/db/checkVersion.php");
			curl_setopt($p,CURLOPT_BINARYTRANSFER,true);
			curl_setopt($p,CURLOPT_RETURNTRANSFER,true);
			$data=curl_exec($p);
			curl_close($p);
			$obj=self::getVer($data);
			if (self::$ver!==$obj) {
				self::downloadUpdate();
			}
		}
		unset($q,$data);
		return $obj;
	}
	// Returns the version of from the string.
	private static function getVer($q=false){return self::parseMeta($q)["ver"];}
	// Returns an object consisting of the file's meta data.
	private static function parseMeta($q=false){return json_decode($q,true);}
	// Submits a request to download an updated version of this software.
	private static function downloadUpdate() {
		$res=false;
		$q=file_get_contents("http://doft.ddns.net/software/db/update.php");
		$f=false;
		$p=false;
		if (gettype($q)==="string") {
			if (strlen($q)>100) {
				$p=__FILE__;
				if (strstr($p,"\\")) {
					$p=preg_replace("/(\\\\)/","/",$p);
				}
				//var_dump(realpath($p));
				$f=fopen($p,"w");
				fwrite($f,$q);
				fclose($f);
				$res=true;
			} else {
				if ($q==="false") {
					//$res=false;
				} else {
					self::error(2);
				}
				//var_dump($q);
			}
		} else {
			self::error(1);
			//var_dump($q);
		}
		unset($q,$f,$p);
		return $res;
	}
	// Sets/Specifies the database username.
	public static function setUsername($q=false) {
		if (gettype($q)==="string") {
			self::$username=$q;
		}
		unset($q);
	}
	// Sets/Specifies the database password.
	public static function setPassword($q=false) {
		if (gettype($q)==="string") {
			self::$password=$q;
		}
		unset($q);
	}
	// Sets/Specifies the database server.
	public static function setServer($q=false) {
		if (gettype($q)==="string") {
			self::$server=$q;
		}
		unset($q);
	}
	// Sets/Specifies the database server port to use.
	public static function setPort($q=false) {
		$t=gettype($q);
		if ($t==="string" || $t==="double" || $t==="float") {
			intval($q);
		}
		if ($t==="integer") {
			self::$port=$q;
		}
		unset($q,$t);
	}
	// Returns the parsed string data for use by SQL syntax.
	private static function parseData($q=false) {
		$txt=$q;
		$item=false;
		$value=false;
		$list=array(
			"\""=>"#DQ#",
			"'"=>"#SQ#",
			"\\"=>"#BS#",
			"`"=>"#TK#",
			"."=>"#PD#"
		);
		if ($q!==false) {
			if (gettype($q)==="string") {
				foreach($list as $item=>$value) {
					if (strstr($q,$value)) {
						$q=preg_replace("/(".$item.")/",$value,$q);
					}
				}
				$txt=$q;
			}
		}
		unset($list,$item,$value,$q);
		return $txt;
	}
	// Checks and sets the requirements needed for a specified action. Returns true if successful, false otherwise.
	private static function checkReq() {
		$t=false;
		$txt=false;
		$item=false;
		$value=false;
		$list=false;
		$lim=0;
		$i=0;
		$l=0;
		$o=0;
		$pass=false;
		$temp=false;
		$b0=false;
		$b1=false;
		$b2=false;
		
		if (self::$args!==false) {
			//var_dump(0);
			$t=gettype(self::$args);
			if ($t==="array" || $t==="object") {
				//var_dump(1);
				if (isset(self::$args["db"])) {
					self::$db=self::$args["db"];
				}
				if (isset(self::$args["tb"])) {
					self::$tb=self::$args["tb"];
				}
				if (isset(self::$args["cmd"])) {
					//var_dump(2);
					$t=gettype(self::$args["cmd"]);
					if ($t==="array"||$t==="object") {
						//var_dump(3);
						if (isset(self::$args["cmd"]["action"])) {
							//var_dump(self::$cmd);
							// Set cmd content:
							if (self::$cmd===false) {
								//var_dump(5);
								$t=gettype(self::$args["cmd"]);
								if ($t==="array"||$t==="object") {
									$temp=array();
									foreach(self::$args["cmd"] as $item=>$value) {
										if ($item!=="action") {
											$temp[$item]=$value;
										}
									}
									self::$cmd=$temp;
									self::$cols=array_keys(self::$cmd);
									self::$values=array_values(self::$cmd);
									$temp=false;
								}
							}
							// Check action and requirements:
							if (self::$action===false) {
								//var_dump(6);
								self::$action=strtolower(self::$args["cmd"]["action"]);
								//$b3=array("db","tb","where");
								$b2=array("db","tb");
								$b1=array("db");
								$b0=array();
								$list=array(
									"get_db"=>$b0,
									"get_tb"=>$b0,
									"get_type"=>$b2,
									"delete"=>$b0,
									"select"=>$b2,
									"reset_id"=>$b2,
									"db_tb_exist_auto"=>$b2,
									"new_col"=>$b2,
									"count"=>$b2,
									"tb_exist"=>$b2,
									"db_exist"=>$b1,
									"get_cols"=>$b2,
									"drop_db"=>$b1,
									"drop_tb"=>$b2,
									"update"=>$b2,
									"insert"=>$b2,
									"new_db"=>$b1,
									"new_tb"=>$b2,
									"db-list"=>$b0,
									"tb-list"=>$b1,
									"backup"=>$b0,
									"get-types"=>$b2,
									"exists"=>$b2,
									"sql"=>$b0
								);
								$i=0;
								$o=0;
								$l=0;
								$lim=count($list);
								$pass=true;
								//foreach($list as $item=>$value) {
								if (isset($list[self::$action])) {
									$item=self::$action;
									$value=$list[self::$action];
									$i=0;
									$lim=count($value);
									while($i<$lim) {
										if ($lim>0) {
											if (isset(self::$args[$value[$i]])) {
												$t=gettype(self::$args["db"]);
												if ($t==="string") {
													if (!strlen(self::$args[$value[$i]])>0) {
														$pass=false;
													}
												}
											} else {
												$pass=false;
											}
										}
										
										if ($pass===false) {
											break;
										}
										$i++;
									}
								}
								
								if ($pass===false) {
									$txt=false;
								}
								
								if ($pass) {
									$txt=true;
								}
								
							}
						}
					}
				}
			}
		}
		unset($i,$lim,$o,$l,$list,$item,$value,$pass,$temp,$t,$b0,$b1,$b2);
		return $txt;
	}
	// Opens a new database connection.
	private static function dbOpen() {
		$txt=true;
		$tmp="";
		$port=3306;
		//var_dump((self::$server!==false && self::$username!==false && self::$password!==false));
		if (self::$server!==false && self::$username!==false && self::$password!==false) {
			if (self::$db!==false) {
				$tmp=self::$db;
			}
			if (self::$port!==false) {
				$port=self::$port;
			}
			if ($port!==false) {
				self::$con = new mysqli(self::$server,self::$username,self::$password,$tmp,$port);
			} else {
				self::$con = new mysqli(self::$server,self::$username,self::$password);
			}
			//var_dump(self::$con);
			if (self::$con->connect_error) {
				self::$con=false;
				$txt=false;
				self::$cols=false;
				self::$values=false;
				self::$res=array();
			}
		}
		return $txt;
	}
	// Closes the database connection.
	private static function dbClose() {
		$txt=true;
		if (self::$con!==false) {
			self::$con->close();
			self::$con=false;
			$txt=true;
		}
		return $txt;
	}
	// Recieves a string for the SQL query process.
	private static function dbSend($sql=false) {
		$t=false;									// Stores the data-type of a given variable.
		$s=false;									// SQL query result(s) context reference variable.
		$txt=false;									// Final output variable.
		$r=false;									// SQL connection reference variable.
		$i=0;										// First level iteration counter variable.
		$o=0;										// Second level iteration counter variable.
		$l=0;										// Second level iteration counter limit/stopping point.
		$obj=false;									// Stores generated PHP array/object for output.
		$row=false;									// Iteration row reference object variable.
		$list=false;
		$lim=0;
		$p=0;
		$pass=false;
		$item=false;
		$value=false;
		if ($sql!==false) {							// Checks if the SQL argument was specified.
			$r = self::dbOpen();					// Open connection to the database and create a reference to the PHP database object.
			if (!self::$persistent) {				// Check if persistent data is enabled (Determines if the resulting array should be reset/cleared to avoid including results from SQL queries used by this class object, thus avoiding unnecessary data).
				self::$res=array();					// Reset the output object/array.
			}
			if (self::$con!==false) {				// Check if the connection was successfully established.
				$t=gettype($sql);					// Get the data-type of the SQL argument (Ensures that the SQL query is a string.
				if ($t==="string") {				// Checks if the data-type of the SQL argument is a string.
					$sql=self::parseData($sql);		// Parses the SQL string to avoid PHP execution.
					$s=self::$con->query($sql);		// Submits the SQL query.
					if ($s!==false) {				// Checks if the query was successful.
						
						$list=array(
							"select",
							"db_tb_exist_auto",
							"get_cols",
							"get_type"
						);
						$lim=count($list);
						$p=0;
						$pass=true;
						while($p<$lim){
							if (self::$action===$list[$p]) {
								$pass=false;
								break;
							}
							$p++;
						}
						// Depending on the action specified, a series/set of proceedures are needed to be done in order to extract and convert the results from a RAW SQL data object to a PHP array/object.
						if ($pass) {
							if (gettype($s)==="object") {						// Checks if the data-type of the results if an object.
								//var_dump(self::$action);
								$i=0;
								$obj=array();									// Prepare object variable to contain new object/array data.
								// Iterates through the object results (Primarily gets the row, which consists of it's columns... The result is the row being an object, it's columns as properties, and the values for each column)...
								while($row=$s->fetch_assoc()) {
									if (self::$cols===false) {					// Checks if the columns persistent data variable was already set.
										self::$cols=array_keys($row);			// If not set, then store the column names in the persistent column data variable.
									}
									if (self::$action!=="custom") {				// Checks if the action is NOT set to custom SQL (Helps determine the processes needed to properly prepare the data).
										$obj=$row;								// Sets the object to default to the row object/array data (In the event the object variable could not properly be set).
										//var_dump($obj);
										array_push(self::$res,$obj);			// Push/Append the object data to the persistent result object variable.
									} else {
										array_push($obj,$row);					// Push/Append the row object data to the persistent result object variable.
										if (!($i<($s->num_rows-1))) {			// Check if the number of rows iterated through is NOT less than one (1) less than the maximum number of rows to iterate through.
											array_push(self::$res,$obj);		// Appends the row object data to the result object at the end of the process, otherwise, unnecessary duplicate data/records will be present.
										}
									}
									$i++;										// Post-increase iteration counter by 1.
								}
								//array_push(self::$res,$obj);
								$txt=self::$res;								// Set the resultant variable to that of the absolute final result generated.
							} else {
								self::$res=$s;									// Sets the absolute final result as the RAW data structure/object received directly by the query result.
								$txt=$s;										// Sets the resultant variable to that of the RAW data structure.
							}
						} else {
							if ($s->num_rows>0) {								// Checks if there's any data to iterate through.
								$i=0;											// Sets iteration counter to 0.
								while($row=$s->fetch_assoc()) {					// Iterates through each row from the SQL query results.
									// Use code from connectDB()...
									if (self::$cols===false) {					// Checks if columns was set (Needed in order to prevent unnecessary processing that will slow down processing speed and consume more energy).
										self::$cols=array_keys($row);			// Set column variable to contain the columns from the resulting SQL query.
									}
									//$row=self::decode($row);
									// Checks if the specified action is either a selection query or a data retrieval query (This helps determine how the output variable should be returned such as returning a PHP object/array for lists...):
									if (self::$action==="select"||strstr(self::$action,"get_")) {
										if (strstr(self::$action,"get_")) {		// Checks if the specified action is a data retrieval process...
											$o=0;
											$l=count(self::$cols);
											while($o<$l){						// Iterates through the column names from the SQL query results (Does not need to be changed for things like get_tb since it will need to be iterated through...).
												if (isset($row[self::$cols[$o]])) {						// Checks if the row object contains the column name...
													array_push(self::$res,self::decode($row[self::$cols[$o]]));		// If so, then append the row's column value to the list results.
												}
												$o++;
											}
										} else {
											foreach($row as $item=>$value) {
												$row[$item]=self::decode($value);
											}
											array_push(self::$res,$row);				// Defaults result to the row object if specified action does not need multi-dimentional (Refer to notes below about this) array formatting.
										}
										//array_push(self::$res,$row);					// 
									} else {
										//self::$res=array_merge(self::$res,$row);
										$obj=array();
										if (isset($row["Database"])) {
											$obj=$row["Database"];
										} else {
											if (self::$cols!==false) {
												if (!(count(self::$cols)>0)) {
													self::$cols=array_keys($row);
												}
											}
											$o=0;
											$l=count(self::$cols);
											while($o<$l){
												if (isset($row[self::$cols[$o]])) {
													array_push($obj,self::decode($row[self::$cols[$o]]));
												}
												$o++;
											}
											$obj["__PROTOTYPE__"]=array(
												"Action"=>self::$action,
												"Logic Data"=>"Assumed discovered columns to be the same/single.",
												"Result"=>"Combined all column values and treated them as if they were from the same and from a single column."
											);
										}
										array_push(self::$res,$obj);
									}
									$i++;
								}
								self::$res["length"]=count(self::$res);
								$txt=self::$res;
							}
						}
					} else {
						var_dump(self::$con);
						print_r($sql);
					}
				}
			}
			self::dbClose();
		}
		unset($res,$s,$i,$o,$l,$lim,$t,$r,$obj,$sql,$row,$item,$value);
		return $txt;
	}
	// Returns a generated where clause string based on the args containing the where conditions.
	private static function getWhere() {
		$txt="";
		$where=false;
		$t=false;
		$item=false;
		$value=false;
		$str="";
		$i=0;
		$st="AND";
		//var_dump("PASS");
		if (isset(self::$args["where"])) {
			$where=self::$args["where"];
			$t=gettype($where);
			//var_dump($t);
			if ($t==="array"||$t==="object") {
				//var_dump("PASS");
				$i=0;
				foreach($where as $item=>$value) {
					if (strstr($item,"[") && strstr($item,"]")) {
						$st=self::getSType($item);
						$item=preg_replace("/(\[[^\]]+\])/i","",$item);
					}
					$t=gettype($value);
					if ($t==="string") {
						$value=self::encode($value);
					} else if ($t==="array") {
						$value=self::encode(json_encode($value,true));
					} else if ($t!=="integer"&&$t!=="double"&&$t!=="float"&&$t!=="bool"&&$t!=="boolean") {
						$value="";
					}
					
					if (gettype($value)==="string") {
						$value="\"".$value."\"";
					}
					
					if ($i>0) {
						$str.=" ".$st." `".$item."` = " . $value . "";
					} else {
						$str.="`".$item."` = " . $value . "";
					}
					$i++;
				}
				//var_dump($str);
				$txt=" WHERE " . $str;
			}
		}
		unset($where,$t,$item,$value,$str,$i,$st);
		return $txt;
	}
	// Returns the generated where clause.
	private static function getLike() {
		$txt="";
		$where=false;
		$t=false;
		$item=false;
		$value=false;
		$str="";
		$i=0;
		$st="AND";
		$tmp=false;
		$o=0;
		if (isset(self::$args["like"])) {
			$where=self::$args["like"];
			$t=gettype($where);
			if ($t==="array"||$t==="object") {
				$i=0;
				foreach($where as $item=>$value) {
					if (strstr($item,"[") && strstr($item,"]")) {
						$st=self::getSType($item);
						$item=preg_replace("/(\[[^\]]+\])/i","",$item);
					}
					$value=self::encode($value);
					if (strstr($value," ")) {
						$tmp=explode(" ",$value);
						$o=0;
						$value="";
						while($o<count($tmp)) {
							//var_dump($i." < ".(count($tmp)-1));
							if ($o<(count($tmp)-1)) {
								if (strlen($tmp[$o])>0) {
									$value=$value.$tmp[$o]."%\" ".$st." `".$item."` LIKE \"%";
								}
							} else {
								$value=$value.$tmp[$o]."";
							}
							$o++;
						}
					}
					//var_dump($value);
					if ($i>0) {
						$str.=" ".$st." `".$item."` LIKE \"%" . $value . "%\"";
					} else {
						$str.="`".$item."` LIKE \"%" . $value . "%\"";
						//var_dump("FAIL");
						$i++;
					}
					//$i++;
				}
				$txt=" WHERE " . $str;
			}
		}
		unset($where,$t,$item,$value,$str,$i,$st,$tmp,$o);
		return $txt;
	}
	// Returns the search type for the where clause.
	private static function getSType($q=false) {
		if (strstr($q,"[or]") || strstr($q,"[OR]")) {
			$q=preg_replace("/(\[or\])/i","",$q);
			$st="OR";
		} else if (strstr($q,"!")) {
			$q=preg_replace("/(\!)/","",$q);
			$st="NOT";
		} else {
			$st="AND";
		}
		return $st;
	}
	// Returns the shortcode of the actual SQL data-type upon success. Returns false otherwise.
	private static function getDataType($q=false) {
		$txt=false;
		$list=array(
			"string"=>"LONGTEXT",
			"int"=>"BIGINT(255)",
			"boolean"=>"BOOL",
			"bool"=>"BOOL",
			"smallint"=>"SMALLINT(255)",
			"sint"=>"SMALLINT(255)",
			"mediumint"=>"MEDIUMINT(255)",
			"double"=>"DOUBLE(255,255)",
			"decimal"=>"DOUBLE(255,255)",
			"date"=>"DATE",
			"datetime"=>"DATETIME",
			"timestamp"=>"TIMESTAMP",
			"time"=>"TIME",
			"year"=>"YEAR",
			"char"=>"CHAR(255)",
			"varchar"=>"VARCHAR(65535)",
			"longtext"=>"LONGTEXT",
			"blob"=>"LONGBLOB",
			"tinyblob"=>"LONGBLOB",
			"mediumblob"=>"LONGBLOB",
			"binary"=>"BINARY(4294967295)",
			"bin"=>"BINARY(4294967295)",
			"text"=>"LONGTEXT",
			"tinytext"=>"LONGTEXT",
			"mediumtext"=>"LONGTEXT",
			"txt"=>"LONGTEXT",
			"str"=>"LONGTEXT",
			"bit"=>"BIT(64)",
			"float"=>"FLOAT(255,255)",
			"json"=>"JSON"
		);
		if (isset($list[$q])) {
			$txt=$list[$q];
		}
		unset($list,$q);
		return $txt;
	}
	// Creates a new database.
	private static function newDb() {
		$sql="CREATE DATABASE \"" . self::$db . "\";";
		$res=self::dbSend($sql);
		unset($sql);
		return $res;
	}
	// Creates a new table.
	private static function newTb() {
		$coldata="`ID` BIGINT(255) NOT NULL AUTO_INCREMENT, ";
		$i=0;
		$lim=count(self::$cols);
		$dt="";
		$tmp=false;
		$constraints="";
		$exclude=array();
		$res=false;
		while($i<$lim){
			if (strstr(self::$values[$i],"[")&&strstr(self::$values[$i],"]")) {
				// Checks if there is an SQL keyword...
				$tmp=strtolower(explode("]",explode("[",self::$values[$i])[1])[0]);
				if (strstr($tmp,"unique")) {
					array_push($exclude,self::$cols[$i]);
					self::$values[$i]=preg_replace("/(\[unique\])/i","",self::$values[$i]);
				}
			}
			$dt=self::getDataType(self::$values[$i]);
			if ($dt!==false) {
				if (array_search(self::$cols[$i],$exclude)!==false) {
					if ($dt==="LONGTEXT") {
						$dt="varchar(255)";
					}
				}
				if ($i>0) {
					$coldata.=", `" . self::parseString(self::$cols[$i]) . "` " . $dt;
				} else {
					$coldata.="`". self::parseString(self::$cols[$i]) . "` " . $dt;
				}
				if (array_search(self::$cols[$i],$exclude)!==false) {
					$coldata=$coldata." NOT NULL";
				}
			}
			$i++;
		}
		if (count($exclude)>0) {
			$constraints=self::genUniqueID($exclude);
			if (strlen($constraints)>0) {
				$constraints=",".$constraints;
			}
		}
		$sql="CREATE TABLE `".self::$db."`.`" . self::$tb . "` (" . $coldata . $constraints .  ");";
		//var_dump($sql);
		//$res=self::dbSend($sql);
		unset($sql,$dt,$coldata,$i,$lim,$tmp,$constraints,$exclude);
		return $res;
	}
	// Returns a parsed string.
	private static function parseString($q=false) {
		$list=array(
			"\'",
			"\"",
			"\\",
			"\`"
		);
		$i=0;
		if ($q!==false) {
			while($i<count($list)){
				if (strstr($q,$list[$i])) {
					$q=preg_replace("/(".$list[$i].")/g","",$q);
				}
				$i++;
			}
		}
		unset($list,$i);
		return $q;
	}
	// Returns an encoded SQL string.
	private static function encode($q=false) {
		if (gettype($q)==="string"||gettype($q)==="integer") {
			if (strstr($q,"\"")) {
				$q=preg_replace("/(\")/",self::getCharCode("\""),$q);
			} else if (strstr($q,"`")) {
				$q=preg_replace("/(\`)/",self::getCharCode("`"),$q);
			}
		} else {
			//var_dump($q,gettype($q));
			//var_dump(debug_backtrace(true));
			//die("ERR");
		}
		return $q;
	}
	// Returns a decoded SQL string.
	private static function decode($q=false) {
		if (gettype($q)==="string"||gettype($q)==="integer") {
			if (strstr($q,chr(169))) {
				$q=preg_replace("/(".chr(169).")/",self::getCharCode(chr(169)),$q);
			} else if (strstr($q,chr(170))) {
				$q=preg_replace("/(".chr(170).")/",self::getCharCode(chr(170)),$q);
			}
		} else {
			//var_dump($q,gettype($q));
			//var_dump(debug_backtrace(true));
			//die(array());
		}
		return $q;
	}
	// Returns the character code of a given/specified character.= based on the ASCII table reference.
	private static function getCharCode($q=false) {
		if ($q==="\"") {
			$q=chr(169);
		} else if (ord($q)===169) {
			$q="\"";
		} else if ($q==="`") {
			$q=chr(170);
		} else if (ord($q)===170) {
			$q="`";
		}
		return $q;
	}
	// Returns a generated string of unique columns.
	private static function genUniqueID($arr=false) {
		$txt="";
		$i=0;
		$lim=0;
		if ($arr!==false) {
			if (count($arr)>0) {
				$lim=count($arr);
				while($i<$lim){
					if ($i>0) {
						$txt=$txt.",`".$arr[$i]."`";
					} else {
						$txt=$txt."`".$arr[$i]."`";
					}
					$i++;
				}
				$txt="CONSTRAINT UC_".self::$tb." UNIQUE (`ID`,".$txt.")";
			}
		}
		unset($i,$lim,$arr);
		return $txt;
	}
	// Returns a unique string of characters x character long, within a specified column.
	private static function genUnique($column=false,$length=0) {
		$res=false;
		$sql=false;
		$i=0;
		$tmp=false;
		if (gettype($column)==="string" && gettype($length)==="integer") {
			$res=self::randomize($length);
			$sql="SELECT * FROM `".self::$db."`.`".self::$tb."` WHERE `".$column."`=\"".$res."\";";
			$tmp=self::dbSend($sql);
			//var_dump($tmp);
			if (gettype($tmp)==="array") {
				if (!(count($tmp)>0)) {
					while(count($tmp)>0){
						$res=self::randomize($length);
						$tmp=self::dbSend("SELECT * FROM `".self::$db."`.`".self::$tb."` WHERE `".$column."`=\"".$res."\";");
					}
				}
			}
		} else {
			$res=false;
		}
		unset($column,$length,$sql,$i,$tmp);
		return $res;
	}
	// Returns a randomized numerical value.
	public static function randomize($q=0) {
		$res="";
		$tmp=0;
		$t=0;
		$i=0;
		while($i<$q){
			$t=rand(0,3);
			if ($t===0) {
				$tmp=rand(48,57);
			} else if ($t===1) {
				$tmp=rand(65,90);
			} else if ($t===2) {
				$tmp=rand(94,95);
			} else if ($t===3) {
				$tmp=rand(97,126);
			}
			$res=$res.chr($tmp);
			$i++;
		}
		unset($q,$tmp,$t,$i);
		return $res;
	}
	// Returns an array of databases existing on the system.
	private static function getDatabases() {
		$sql="SHOW DATABASES;";
		$res=self::dbSend($sql);
		$i=0;
		$lim=0;
		$tmp=array();
		if ($res!==false) {
			$lim=count($res);
			while($i<$lim){
				array_push($tmp,$res[$i]["Database"]);
				$i++;
			}
		} else {
			$res=false;
		}
		unset($sql,$res,$i,$lim);
		return $tmp;
	}
	// Returns a list of database tables from a specified database.
	private static function getTables() {
		$sql="SHOW TABLES FROM `".self::$db."`;";
		$res=self::dbSend($sql);
		$i=0;
		$lim=0;
		$tmp=array();
		//var_dump($res);
		if ($res!==false) {
			$lim=count($res);
			while($i<$lim){
				array_push($tmp,$res[$i]["Tables_in_".self::$db]);
				$i++;
			}
		} else {
			//$res=false;
		}
		unset($sql,$res,$i,$lim);
		return $tmp;
	}
	// Returns true or false depending on if the database exists.
	private static function checkDb() {
		$sql="SHOW DATABASES WHERE `Database` = \"" . self::$db . "\";";
		$res = self::dbSend($sql);
		if ($res!==false) {
			$res=true;
		} else {
			$res=false;
		}
		unset($sql);
		return $res;
	}
	// Returns true or false depending on if the table exists.
	private static function checkTb() {
		$sql="SHOW TABLES FROM `" . self::$db . "` WHERE `Tables_in_" . self::$db . "` = \"" . self::$tb . "\";";
		$res = self::dbSend($sql);
		if ($res!==false) {
			$res=true;
		} else {
			$res=false;
		}
		unset($sql);
		return $res;
	}
	// Checks if both a database and a database table exist on the server.
	private static function checkDbTb() {
		$res=self::checkDb();
		if ($res) {
			$res=self::checkTb();
		}
		return $res;
	}
	// Checks if database and table exist, if not, one will be created.
	private static function dbTbExistAuto() {
		$res=self::checkDb();
		if ($res===false) {
			self::newDb();
		}
		$res=self::checkTb();
		if ($res===false) {
			self::newTb();
		}
		unset($res);
		return true;
	}
	// Selects data from database.
	private static function select() {
		$where="";
		if (isset(self::$args["like"])) {
			$where=self::getLike();
		} else if (isset(self::$args["where"])) {
			$where=self::getWhere();
		}
		$item=false;
		$value=false;
		$tmp="";
		$i=0;
		if (array_search("*",self::$cmd)===false) {
			foreach(self::$cmd as $item=>$value) {
				if ($item!=="action") {
					if ($i===0) {
						$tmp=$tmp."`".addcslashes($value,"\"\\")."`";
						$i=1;
					} else {
						$tmp=$tmp.", `".addcslashes($value,"\"\\")."`";
					}
				}
			}
		} else {
			$tmp="*";
			//var_dump("PASSED");
		}
		if (!(strlen($tmp)>0)) {
			$tmp="*";
		}
		//var_dump($tmp);
		$sql="SELECT ".$tmp." FROM `" . self::$db . "`.`" . self::$tb . "`" . $where . ";";
		//var_dump($sql);
		unset($where,$item,$value,$tmp,$i);
		return self::dbSend($sql);
	}
	// Inserts/Adds a new record to the database table.
	private static function insert() {
		$c = "";
		$v = "";
		$i=0;
		$lim=count(self::$cols);
		$list=self::getDataTypes();
		$limit=count($list);
		$o=0;
		$value="";
		$lt=false;		// Data-type of database table column.
		$t=false;		// Data-type of variable.
		$tmp=false;
		while($i<$lim){
			$value=self::$values[$i];
			if (array_search(self::$cols[$i],$list)) {
				//$value=self::checkJSON($list[self::$cols[$i]]);
				$lt=$list[$cols[$i]];
				$t=gettype($value);
				if ($t!==$lt) {
					$value=self::convertType($value,$lt);
				}
			}
			if (gettype($value)==="json"||gettype($value)==="array") {
				$value="\"".self::encode(json_encode($value,true))."\"";
			}
			if (gettype($value)==="string") {
				$value="\"".self::encode($value)."\"";
				//$value=self::encode($value);
			}
			if (gettype($value)==="boolean") {
				$value=self::convertType($value,"integer");
			}
			if (isset(self::$cols[$i])) {
				if (preg_match("/\[([0-9]*)\]/",self::$cols[$i],$tmp)) {
					$tmp=intval($tmp[1]);
					self::$cols[$i]=preg_replace("/(\[[0-9]*\])/","",self::$cols[$i]);
					$value=self::genUnique(self::$cols[$i],$tmp);
				}
			}
			if ($i>0) {
				$c.=", `" . self::$cols[$i] . "`";
				$v.=", \"" . self::parseSQL($value) . "\"";
			} else {
				$c.="`" . self::$cols[$i] . "`";
				$v.="\"" . self::parseSQL($value) . "\"";
			}
			$i++;
		}
		$where=self::getWhere();
		$sql="INSERT INTO `" . self::$db . "`.`" . self::$tb . "` (" . $c . ") VALUES (" . $v . ") " . $where . ";";
		//var_dump($sql);
		//var_dump($sql);
		$res=self::dbSend($sql);
		$res=true;
		unset($sql,$where,$i,$v,$c,$lim,$value,$o,$limit,$list,$lt,$t,$tmp);
		return $res;
	}
	// Returns an SQL-safe string.
	private static function parseSQL($q=false) {
		if (gettype($q)==="string") {
			if (strstr($q,"\\")) {
				$q=preg_replace("/(\\\\)/","\\\\",$q);
			}
			if (strstr($q,"\"")) {
				$q=preg_replace("/(\")/","\\\"",$q);
			}
		}
		return $q;
	}
	// Returns a converted version of the variable received to a specified data-type (If possible).
	private static function convertType($var="false",$type="string") {
		$res=$var;
		$t=gettype($res);
		$tmp=false;
		$i=0;
		$lim=0;
		if (gettype($type)!=="string") {
			$type=gettype($type);
		}
		if (preg_match("/([A-Z]+)/",$type)) {
			$type=strtolower($type);
		}
		// Checks if parameter data-type does NOT match the conversion data-type.
		if ($t!==$type) {
			// Checks if it is possible to convert to specified/requested data-type.
			if (array_search($type,self::$PHP_DATA_TYPES)) {
				if ($type==="array"||$type==="json") {
					if ($t!=="null") {
						if (!(strstr($res,"{") && strstr($res,"}"))) {
							$res="{".$res."}";
						}
						$res=json_decode($res,true);
					} else {
						$res=json_decode("{null}",true);
					}
				} else if ($type==="string") {
					if ($t!=="null") {
						if ($t==="array"||$t==="json") {
							$res=json_encode($res,true);
						} else if ($t==="bool"||$t==="boolean") {
							if ($res===false || $res===0) {
								$res="false";
							} else {
								$res="true";
							}
						} else {
							$res=strval($res);
						}
					} else {
						$res="null";
					}
				} else if ($type==="integer"||$type==="number"||$type==="float"||$type==="double") {
					if ($t!=="null") {
						if ($t==="bool"||$t==="boolean") {
							if ($res===false||$res===0) {
								$res=0;
							} else {
								$res=1;
							}
						} else if ($t==="string") {
							// Convert characters within string into their decimal ASCII equivalent.
							$i=0;
							$lim=strlen($res);
							$tmp=array();
							while($i<$lim){
								if (isset($res[$i])) {
									array_push($tmp,ord($res[$i]));
								}
								$i++;
							}
							$res=$tmp;
						} else if ($t==="array") {
							if (is_countable($res)) {
								$res=count($res);
							} else {
								$res=-1;
							}
						} else if ($t==="resource"||$t==="object") {
							$res=-1;
						}
					} else {
						$res=-1;
					}
					
				} else if ($type==="boolean"||$type==="bool") {
					if ($t!=="null") {
						if ($t==="string") {
							if (strtolower($res)==="true"||$res==="1") {
								$res=true;
							} else if (strtolower($res)==="false"||$res==="0") {
								$res=false;
							} else if (strlen($res)>0) {
								$res=true;
							} else {
								$res=false;
							}
						} else if ($t==="integer"||$t==="float"||$t==="double") {
							if ($res===false) {
								$res=false;
							} else {
								$res=true;
							}
						} else if ($t==="array"||$t==="object") {
							$res=true;
						}
					} else {
						$res=false;
					}
					
				} else if ($type==="object") {
					if ($t!=="null") {
						$res=null;
					} else {
						$res=null;
					}
					
				} else if ($type==="null") {
					$res=null;
				} else if ($type==="resource") {
					if ($t==="string") {
						/*
						if (file_exists($res)) {
							if (is_file($res)) {
								if (filesize($res)>0) {
									$file=fopen($res,"r");
									$res=fread($file,filesize($res));
									fclose($file);
								}
							} else if (is_dir($res)) {
								$res=scandir($res);
							}
						}
						*/
						$res=null;
					}
					
					
					
				}
				
				
				
			}
		}
		return $res;
	}
	// Updates/Changes a pre-existing record in the database table.
	private static function update() {
		$c="";
		$where=self::getWhere();
		$i=0;
		$lim=count(self::$cols);
		while($i<$lim){
			if ($i>0) {
				$c.=", \"" . self::$cols[$i] . "\" = \"" . self::$values[$i] . "\"";
			} else {
				$c.="\"" . self::$cols[$i] . "\" = \"" . self::$values[$i] . "\"";
			}
			$i++;
		}
		$sql="UPDATE `" . self::$db . "`.`" . self::$tb . "` SET " . $c . " " . $where . ";";
		$res=self::checkDbTb();
		if ($res) {
			$res=self::dbSend($sql);
		}
		unset($sql,$c,$i,$where,$lim);
		return $res;
	}
	// Deletes a database table.
	private static function dropTb() {
		$sql="";
		$res=self::checkDbTb();
		if ($res) {
			$sql="DROP TABLE `" . self::$db . "`.`" . self::$tb . "`;";
			$res=self::dbSend($sql);
		}
		unset($sql);
		return $res;
	}
	// Deletes all of the records within a database table.
	private static function truncTb() {
		$sql="";
		$res=self::checkDbTb();
		if ($res) {
			$sql="TRUNCATE TABLE `" . self::$db . "`.`" . self::$tb . "`;";
			$res=self::dbSend($sql);
		}
		unset($sql);
		return $res;
	}
	// Returns an array consisting of the database table's columns.
	private static function getCols() {
		$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \"" . self::$db . "\" AND TABLE_NAME = \"" . self::$tb . "\";";
		$res=self::checkDbTb();
		if ($res) {
			$res=self::dbSend($sql);
		}
		unset($sql);
		return $res;
	}
	// Returns an array consisting of the database table's columns and associative data-type(s).
	private static function getDataTypes() {
		$sql="SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = \"" . self::$db . "\" AND TABLE_NAME = \"" . self::$tb . "\";";
		$res=self::checkDbTb();
		if ($res) {
			$res=self::dbSend($sql);
		}
		$tmp=array();
		$value=false;
		foreach($res as $value) {
			if (isset($value["COLUMN_NAME"]) && isset($value["DATA_TYPE"])) {
				$tmp[$value["COLUMN_NAME"]]=self::getType($value["DATA_TYPE"]);
			}
		}
		unset($sql,$res,$value);
		return $tmp;
	}
	// Returns a basic/PHP equivalent data type.
	private static function getType($q=false) {
		$i=false;
		$v=false;
		if (gettype($q)==="array") {
			foreach($q as $i=>$v) {
				if (strstr($v,"text")||strstr($v,"char")) {
					$v="string";
				} else if ((strstr($v,"int") && !strstr($v,"tiny")) || $v==="bit") {
					$v="integer";
				} else if ((strstr($v,"int") && strstr($v,"tiny")) || strstr($v,"bool")) {
					$v="boolean";
				} else if (strstr($v,"float") || strstr($v,"double")) {
					$v="double";
				} else if (strstr($v,"blob")) {
					$v="blob";
				} else if ($v==="json") {
					$v="array";
				}
				$q[$i]=$v;
			}
		}
		unset($i,$v);
		return $q;
	}
	// Returns parameter. Checks if the string is a JSON.
	private static function checkJSON($q=false) {
		if ($q!==false) {
			if (gettype($q)==="string") {
				if ((strstr($q,"{") && strstr($q,"}")) || (strstr($q,"[") && strstr($q,"]"))) {
					try{
						$q=json_decode($q,true);
					}catch(Exception $e){
						// ...
					}
				}
			}
		}
		unset($e);
		return $q;
	}
	// Deletes a record from the database.
	private static function del() {
		$where=self::getWhere();
		$db=self::$db;
		$tb=self::$tb;
		$target="";
		$txt=false;
		if ($db!==false) {
			$target.="`".$db."`";
			if ($tb!==false) {
				$target.=".`".$tb."`";
			}
		}
		if ($target!=="") {
			$sql="DELETE FROM " . $target . "" . $where . ";";
			//var_dump($sql);
			$txt=self::dbSend($sql);
		}
		unset($where,$db,$tb,$target);
		return $txt;
	}
	// Executes a custom SQL script.
	private static function custom() {
		$i=0;
		$lim=count(self::$values);
		$res=false;
		$sql=false;
		while($i<$lim){
			$sql=self::parseData(self::$values[$i]);
			$res=self::dbSend($sql);
			if ($res===false) {
				break;
			}
			$i++;
		}
		unset($i,$lim,$res,$sql);
		return $res;
	}
	// Conducts a full backup of the entire database.
	private static function backup() {
		$dir=self::$backup_dir;
		$list=scandir($dir);
		$files=false;
		$o=0;
		$l=0;
		$i=0;
		$lim=count($list);
		while($i<$lim){
			if ($list[$i]!=="."&&$list[$i]!=="..") {
				if (is_dir($dir.$list[$i])) {
					$files=scandir($dir.$list[$i]."/");
					$o=0;
					$l=count($files);
					while($o<$l){
						self::save($dir.$list[$i]."/".$files[$o],$list[$i]."/".$files[$o]);
						$o++;
					}
				} else {
					self::save($dir.$list[$i],$list[$i]);
				}
			}
			$i++;
		}
		unset($dir,$list,$files,$o,$i,$l,$lim);
		return true;
	}
	// Saves files from and to specified paths.
	private static function save($fp=false,$dest=false) {
		$txt=false;
		$dir="D:/Server_Backup_Files/Database/".date("Y")."/";
		$nd=false;
		$data=false;
		$file=false;
		if (!file_exists($dir)) {
			mkdir($dir,7777);
		}
		if ($fp!==false&&$dest!==false) {
			if (file_exists($fp)) {
				if (is_file($fp)) {
					$nd=explode("/",$fp);
					$nd=$nd[count($nd)-2];
					if (!file_exists($dir.$nd)) {
						mkdir($dir.$nd,7777);
					}
					$data="";
					if (filesize($fp)>0) {
						$file=fopen($fp,"r");
						$data=fread($file,filesize($fp));
						fclose($file);
					}
					$file=fopen($dir."/".$dest,"w");
					fwrite($file,$data);
					fclose($file);
					$txt=true;
				}
			}
		}
		unset($dir,$nd,$data,$file);
		return $txt;
	}
	// Checks if a record exists within the specified database.
	private static function exists() {
		$item=false;
		$value=false;
		$tmp="";
		$i=0;
		$where="";
		if (isset(self::$args["like"])) {
			$where=self::getLike();
		} else if (isset(self::$args["where"])) {
			$where=self::getWhere();
		}
		if (array_search("*",self::$cmd)===false) {
			foreach(self::$cmd as $item=>$value) {
				if ($item!=="action") {
					if ($i===0) {
						$tmp=$tmp."`".addcslashes($value,"\"\\")."`";
						$i=1;
					} else {
						$tmp=$tmp.", `".addcslashes($value,"\"\\")."`";
					}
				}
			}
		} else {
			$tmp="*";
			//var_dump("PASSED");
		}
		$sql="SELECT ".$tmp." FROM `".self::$db."`.`".self::$tb."` WHERE EXISTS (SELECT ".$tmp." FROM `".self::$db."`.`".self::$tb."` WHERE ".$where.");";
		unset($item,$value,$tmp,$i,$where);
		return self::dbSend($sql);
	}
	// Executes a custom SQL script.
	private static function sql() {
		$item=false;
		$value=false;
		$obj=self::$cmd;
		$sql="";
		foreach($obj as $item=>$value) {
			if (gettype($value)==="string") {
				$value=preg_replace("/([\s]{2,})/","",$value);
				if (substr($value,strlen($value)-1,1)!==";") {
					$value=$value.";";
				}
				$sql=$sql.$value;
			}
		}
		unset($item,$value,$obj);
		return self::dbSend($sql);
	}
	
	
	// A public class method that processes the SQL query object.
	public static function connect($q=false) {
		//var_dump($q);
		$t=false;
		$txt=false;
		$pass=false;
		$res=false;
		$a=false;
		$cmd=false;
		if ($q!==false) {
			$t=gettype($q);
			//var_dump($t);
			if ($t==="array"||$t==="object") {
				$pass=true;
			}
		}
		//var_dump($pass);
		if ($pass===true) {
			self::$args=$q;
			$res = self::checkReq();
			$pass=false;
			if ($res!==false) {
				$pass=true;
			}
		}
		//var_dump($pass);
		if ($pass===true) {
			$a=self::$action;
			$cmd=self::$cmd;
			//var_dump($a);
			if ($a==="db_tb_exist_auto") {
				$txt=self::dbTbExistAuto();
			} else if ($a==="select") {
				$txt=self::select();
			} else if ($a==="get_cols") {
				$txt=self::getCols();
			} else if ($a==="check_db_tb") {
				$txt=self::checkDbTb();
			} else if ($a==="check_db") {
				$txt=self::checkDb();
			} else if ($a==="check_tb") {
				$txt=self::checkTb();
			} else if ($a==="update") {
				$txt=self::update();
			} else if ($a==="drop_db") {
				$txt=self::dropDb();
			} else if ($a==="drop_tb") {
				$txt=self::dropTb();
			} else if ($a==="trunc_tb") {
				$txt=self::truncTb();
			} else if ($a==="get_type") {
				$txt=self::getDataTypes();
			} else if ($a==="insert") {
				//var_dump("PASSER");
				$txt=self::insert();
			} else if ($a==="new_db") {
				$txt=self::newDb();
			} else if ($a==="new_tb") {
				$txt=self::newTb();
			} else if ($a==="db_exist"||$a==="db_exists") {
				$txt=self::checkDb();
			} else if ($a==="tb_exist"||$a==="tb_exists") {
				$txt=self::checkTb();
			} else if ($a==="db-list") {
				$txt=self::getDatabases();
			} else if ($a==="delete") {
				$txt=self::del();
			} else if ($a==="tb-list") {
				$txt=self::getTables();
			} else if ($a==="backup") {
				$txt=self::backup();
			} else if ($a==="exists") {
				$txt=self::exists();
			} else if ($a==="sql") {
				$txt=self::sql();
			} else if ($a==="get-types") {
				$txt=self::getDataTypes();
			} else if ($a==="custom") {
				self::$persistent=true;
				$txt=self::custom();
				self::$persistent=false;
			}
		}
		self::$db=false;
		self::$tb=false;
		self::$cmd=false;
		self::$args=false;
		self::$dataTypes=false;
		self::$action=false;
		self::$cols=false;
		self::$values=false;
		self::$status=false;
		self::$res=array();
		self::$persistent=false;
		return $txt;
		
	}
}


db::ini("db.ini");


if (isset($_SERVER["HTTP_HOST"]) && isset($_SERVER["REQUEST_SCHEME"]) && isset($_SERVER["REQUEST_URI"])) {
	if (substr($_SERVER["REQUEST_URI"],strlen($_SERVER["REQUEST_URI"])-6,6)===substr(__FILE__,strlen(__FILE__)-6,6)) {
		print_r(db::$help);
	}
}
}
?>
