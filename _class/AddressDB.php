<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class AddressDB {

	private $id = null;
	private $employee_id = null;
	private $applicant_id = null;
	private $addr1 = null;
	private $addr2 = null;
	private $city = null;
	private $state = null;
	private $zip_code = null;

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

	public function set_applicant_id($applicant_id) {
		$this->applicant_id = $applicant_id;
	}
	public function get_applicant_id() {
		return $this->applicant_id;
	}

	public function set_addr1($addr1) {
		$this->addr1 = $addr1;
	}
	public function get_addr1() {
		return $this->addr1;
	}

	public function set_addr2($addr2) {
		$this->addr2 = $addr2;
	}
	public function get_addr2() {
		return $this->addr2;
	}

	public function set_city($city) {
		$this->city = $city;
	}
	public function get_city() {
		return $this->city;
	}

	public function set_state($state) {
		$this->state = $state;
	}
	public function get_state() {
		return $this->state;
	}

	public function set_zip_code($zip_code) {
		$this->zip_code = $zip_code;
	}
	public function get_zip_code() {
		return $this->zip_code;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM address WHERE $where";

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

	public static function populate($id = null, $employee_id = null, $applicant_id = null, $addr1 = null, $addr2 = null, $city = null, $state = null, $zip_code = null) {
		$item = new address();
		$item->set_id($id);
		$item->set_employee_id($employee_id);
		$item->set_applicant_id($applicant_id);
		$item->set_addr1($addr1);
		$item->set_addr2($addr2);
		$item->set_city($city);
		$item->set_state($state);
		$item->set_zip_code($zip_code);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_employee_id(),
			$this->get_applicant_id(),
			$this->get_addr1(),
			$this->get_addr2(),
			$this->get_city(),
			$this->get_state(),
			$this->get_zip_code(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO address(id, employee_id, applicant_id, addr1, addr2, city, state, zip_code) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE address SET id = ?, employee_id = ?, applicant_id = ?, addr1 = ?, addr2 = ?, city = ?, state = ?, zip_code = ? WHERE id = ?";
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
		$sql = "DELETE FROM address WHERE id = ?";
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