<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatesDB {

	private $id = null;
	private $abbr = null;
	private $name = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_abbr($abbr) {
		$this->abbr = $abbr;
	}
	public function get_abbr() {
		return $this->abbr;
	}

	public function set_name($name) {
		$this->name = $name;
	}
	public function get_name() {
		return $this->name;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM states WHERE $where";

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

	public static function populate($id = null, $abbr = null, $name = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_abbr($abbr);
		$item->set_name($name);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_abbr(),
			$this->get_name(),
		);

		if ($this->get_id() == null) {
			unset($params[0]);
			$params = array_values($params);
			$sql = "INSERT INTO states(id, abbr, name) VALUES (null, ?, ?)";
		} else {
			$sql = "UPDATE states SET id = ?, abbr = ?, name = ? WHERE id = ?";
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
		$sql = "DELETE FROM states WHERE id = ?";
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