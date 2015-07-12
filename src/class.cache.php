<?php
/*
* Clase Cache
* carlos de oliveira
* cardoel@gmail.com
*/

class cache
{
	var $cache_dir; 
	var $cache_time;
	var $caching = false; 
	var $cleaning = false; 
	var $file = ''; 
	var $key;
	var $keylink;
	
	function __construct() {	
		$this->cache_time = 20;
		$this->cache_dir = CACHEDIR;	
		$this->cleaning = NULL;
		$this->key = "lsjdhfsd4sdaaa";	
		$this->keylink = $_SERVER['REQUEST_URI'];
	}
	
	function cache() {
		$this->cache_time = 20;
		$this->cache_dir = CACHEDIR;	
		$this->cleaning = NULL;
	}
	
	function setKeyLink($link) {
		$this->keylink = $link;
	}

	
	function setTime($seconds) {
		$this->cache_time = $seconds;	
	}
	
	public function getFile() {
		return $this->cache_dir."cache_".md5($this->key.$this->keylink).".html"; 
	}
	
	public function getCache() {
		return $this->start(true);	
	}
	
	
	private function getContents($path, $waitIfLocked = true) {
		if(!file_exists($path)) return "";
		$fo = fopen($path, 'r');
		$locked = flock($fo, LOCK_SH, $waitIfLocked);	   
		if(!$locked) {
			return false;
		}
		else {
			$cts = file_get_contents($path);		   
			flock($fo, LOCK_UN);
			fclose($fo);		   
			return $cts;
		}
	} 
	
	function start($return = false)	{				
		$this->file = $this->getFile();
		if (file_exists($this->file) && (
			fileatime($this->file)+$this->cache_time)>time() && 
			$this->cleaning == false)	{
			$data = $this->getContents($this->file);
			//$data = bzdecompress($data);
			if($return) return $data;
			echo $data;
			exit();
		} else {		
			$this->caching = true;
			return false;
		}
	}
	
	public function saveOutput($data) {
		$this->finish($data,false);
	}
	
	function finish($data, $echo = true){
		if ($this->caching){
			if($echo) echo $data;
			if(file_exists($this->file)) unlink($this->file);
			$fp = fopen( $this->file , 'w' );
			//$data = bzcompress($data);
			//fwrite ( $fp , bzcompress($data) );
			fwrite ( $fp , $data );
			fclose ( $fp );
		}
	}	 
} 
?>