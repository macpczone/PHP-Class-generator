<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class EmployeeDB {

	private $id = null;
	private $first_name = null;
	private $mid_init = null;
	private $last_name = null;
	private $email = null;
	private $date_of_birth = null;
	private $employment_date = null;
	private $status_date = null;
	private $employment_status = null;
	private $ssn = null;
	private $created_date = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
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

	public function set_email($email) {
		$this->email = $email;
	}
	public function get_email() {
		return $this->email;
	}

	public function set_date_of_birth($date_of_birth) {
		$this->date_of_birth = $date_of_birth;
	}
	public function get_date_of_birth() {
		return $this->date_of_birth;
	}

	public function set_employment_date($employment_date) {
		$this->employment_date = $employment_date;
	}
	public function get_employment_date() {
		return $this->employment_date;
	}

	public function set_status_date($status_date) {
		$this->status_date = $status_date;
	}
	public function get_status_date() {
		return $this->status_date;
	}

	public function set_employment_status($employment_status) {
		$this->employment_status = $employment_status;
	}
	public function get_employment_status() {
		return $this->employment_status;
	}

	public function set_ssn($ssn) {
		$this->ssn = $ssn;
	}
	public function get_ssn() {
		return $this->ssn;
	}

	public function set_created_date($created_date) {
		$this->created_date = $created_date;
	}
	public function get_created_date() {
		return $this->created_date;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM employee WHERE $where";

		try{
			 $dbh = DB::getPdo(); 
			 $sth = $dbh->prepare($sql); 
			 $sth->execute($params); 
		} catch(PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}

		return $sth->fetchAll(PDO::FETCH_CLASS, get_class());
	}

	public static function populate($id = null, $first_name = null, $mid_init = null, $last_name = null, $email = null, $date_of_birth = null, $employment_date = null, $status_date = null, $employment_status = null, $ssn = null, $created_date = null) {
		$item = new employee();
		$item->set_id($id);
		$item->set_first_name($first_name);
		$item->set_mid_init($mid_init);
		$item->set_last_name($last_name);
		$item->set_email($email);
		$item->set_date_of_birth($date_of_birth);
		$item->set_employment_date($employment_date);
		$item->set_status_date($status_date);
		$item->set_employment_status($employment_status);
		$item->set_ssn($ssn);
		$item->set_created_date($created_date);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_first_name(),
			$this->get_mid_init(),
			$this->get_last_name(),
			$this->get_email(),
			$this->get_date_of_birth(),
			$this->get_employment_date(),
			$this->get_status_date(),
			$this->get_employment_status(),
			$this->get_ssn(),
			$this->get_created_date(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO employee(id, first_name, mid_init, last_name, email, date_of_birth, employment_date, status_date, employment_status, ssn, created_date) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE employee SET id = ?, first_name = ?, mid_init = ?, last_name = ?, email = ?, date_of_birth = ?, employment_date = ?, status_date = ?, employment_status = ?, ssn = ?, created_date = ? WHERE id = ?";
			$params[] = $this->id;
		}

		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute($params);
		} catch(PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}
		return true; 
	}

	public function delete() {
		$sql = "DELETE FROM employee WHERE id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->id));
		} catch(PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}