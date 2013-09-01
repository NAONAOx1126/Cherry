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
 * MySQLのクエリ実行結果を管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Result{
	private $resource;
	
	public function __construct($resource){
		$this->resource = $resource;
	}
	
	public function fetch(){
		if($this->resource != null){
			return mysqli_fetch_assoc($this->resource);
		}
		return NULL;
	}
	
	public function fetchAll(){
		$result = array();
		while($data = $this->fetch()){
			$result[] = $data;
		}
		return $result;
	}
	
	public function rewind(){
		if($this->count() > 0){
			mysqli_field_seek($this->resource, 0);
		}
	}
	
	public function count(){
		return mysqli_num_rows($this->resource);
	}
	
	public function close(){
		mysqli_free_result($this->resource);
		$this->resource = null;
	}
}
 