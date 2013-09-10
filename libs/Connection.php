<?php
/**
 * This file is part of Twitter auto post application.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * MySQLのコネクションを管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Connection{
	private $connection;
	
	public function __construct(){
		if(!defined("DATABASE_PORT")){
			define("DATABASE_PORT", "3306");
		}
		$this->connection = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_PORT);
		if(mysqli_set_charset($this->connection, "utf8") === FALSE){
			die("Can't set charcter set");
		}
	}
	
	public function columns($table){
		// テーブルの定義を取得
		if(($result = $this->query("SHOW COLUMNS FROM ".$table)) === FALSE){
			throw new Clay_Exception_System("カラムの取得に失敗しました。");
		}
		$columns = array();
		while($column = $result->fetch()){
			$columns[] = $column;
		}
		$result->close();
		return $columns;
	}
	
	public function keys($table){
		$result = $this->query("SHOW INDEXES FROM ".$table." WHERE Key_name = 'PRIMARY'");
		$keys = array();
		while($key = $result->fetch()){
			$keys[] = $key["Column_name"];
		}
		$result->close();
		return $keys;
	}
	
	public function indexes($table){
		$result = $this->query("SHOW INDEXES FROM ".$table);
		$indexes = array();
		while($index = $result->fetch()){
			if(!isset($indexes[$index["Key_name"]]) || !is_array($indexes[$index["Key_name"]])){
				$indexes[$index["Key_name"]] = array();
			}
			$indexes[$index["Key_name"]][] = $index["Column_name"];
		}
		$result->close();
		return $indexes;
	}
	
	public function relations($table){
		$result = $this->query("SHOW INDEXES FROM ".$table);
		$indexes = array();
		while($index = $result->fetch()){
			if(!isset($indexes[$index["Key_name"]]) || !is_array($indexes[$index["Key_name"]])){
				$indexes[$index["Key_name"]] = array();
			}
			$indexes[$index["Key_name"]][] = $index["Column_name"];
		}
		$result->close();
		return $indexes;
	}
	
	public function begin(){
		$this->query("BEGIN");
	}
	
	public function commit(){
		$this->query("COMMIT");
	}
	
	public function rollback(){
		$this->query("ROLLBACK");
	}
	
	public function escape($value){
		if($this->connection != null){
			return str_replace("\\\"", "\"", mysqli_real_escape_string($this->connection, $value));
		}
		return null;
	}
	
	public function escape_identifier($identifier){
		return "`".$identifier."`";
	}
	
	public function query($query){
		if($this->connection != null){
			mysqli_ping($this->connection);
			$result = mysqli_query($this->connection, $query);
			if($result === FALSE){
				return FALSE;
			}elseif($result !== TRUE){
				return new Result($result);
			}else{
				return mysqli_affected_rows($this->connection);
			}
		}
		return null;
	}
	
	public function auto_increment(){
		return mysqli_insert_id($this->connection);
	}
	
	public function close(){
		if($this->connection != null){
			mysqli_close($this->connection);
			$this->connection = null;
		}
	}
}
 