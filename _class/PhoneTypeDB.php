<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhoneTypeDB {

	private $id = null;
	private $type = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_type($type) {
		$this->type = $type;
	}
	public function get_type() {
		return $this->type;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM phone_type WHERE $where";

		try{
			 $dbh = DB::getPdo(); 
			 $sth = $dbh->prepare($sql); 
			 $sth->execute($params); 
		} catch(\PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}

		return $sth->fetchAll(\PDO::FETCH_CLASS, get_class());
	}

	public static function populate($id = null, $type = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_type($type);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_type(),
		);

		if ($this->get_id() == null) {
			unset($params[0]);
			$params = array_values($params);
			$sql = "INSERT INTO phone_type(id, type) VALUES (null, ?)";
		} else {
			$sql = "UPDATE phone_type SET id = ?, type = ? WHERE id = ?";
			$params[] = $this->id;
		}

		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute($params);
		} catch(\PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}
		return true; 
	}

	public function delete() {
		$sql = "DELETE FROM phone_type WHERE id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->id));
		} catch(\PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}