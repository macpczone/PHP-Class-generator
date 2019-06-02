<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DependentsDB {

	private $id = null;
	private $employee_id = null;
	private $first_name = null;
	private $mid_init = null;
	private $last_name = null;
	private $relationship = null;
	private $date_of_birth = null;
	private $ssn = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_employee_id($employee_id) {
		$this->employee_id = $employee_id;
	}
	public function get_employee_id() {
		return $this->employee_id;
	}

	public function set_first_name($first_name) {
		$this->first_name = $first_name;
	}
	public function get_first_name() {
		return $this->first_name;
	}

	public function set_mid_init($mid_init) {
		$this->mid_init = $mid_init;
	}
	public function get_mid_init() {
		return $this->mid_init;
	}

	public function set_last_name($last_name) {
		$this->last_name = $last_name;
	}
	public function get_last_name() {
		return $this->last_name;
	}

	public function set_relationship($relationship) {
		$this->relationship = $relationship;
	}
	public function get_relationship() {
		return $this->relationship;
	}

	public function set_date_of_birth($date_of_birth) {
		$this->date_of_birth = $date_of_birth;
	}
	public function get_date_of_birth() {
		return $this->date_of_birth;
	}

	public function set_ssn($ssn) {
		$this->ssn = $ssn;
	}
	public function get_ssn() {
		return $this->ssn;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM dependents WHERE $where";

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

	public static function populate($id = null, $employee_id = null, $first_name = null, $mid_init = null, $last_name = null, $relationship = null, $date_of_birth = null, $ssn = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_employee_id($employee_id);
		$item->set_first_name($first_name);
		$item->set_mid_init($mid_init);
		$item->set_last_name($last_name);
		$item->set_relationship($relationship);
		$item->set_date_of_birth($date_of_birth);
		$item->set_ssn($ssn);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_employee_id(),
			$this->get_first_name(),
			$this->get_mid_init(),
			$this->get_last_name(),
			$this->get_relationship(),
			$this->get_date_of_birth(),
			$this->get_ssn(),
		);

		if ($this->get_id() == null) {
			unset($params[0]);
			$params = array_values($params);
			$sql = "INSERT INTO dependents(id, employee_id, first_name, mid_init, last_name, relationship, date_of_birth, ssn) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE dependents SET id = ?, employee_id = ?, first_name = ?, mid_init = ?, last_name = ?, relationship = ?, date_of_birth = ?, ssn = ? WHERE id = ?";
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
		$sql = "DELETE FROM dependents WHERE id = ?";
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