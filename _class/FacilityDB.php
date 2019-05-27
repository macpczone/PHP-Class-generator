<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class FacilityDB {

	private $id = null;
	private $facility_name = null;
	private $location_state = null;
	private $location_city = null;
	private $manager_employee_id = null;
	private $created_date = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_facility_name($facility_name) {
		$this->facility_name = $facility_name;
	}
	public function get_facility_name() {
		return $this->facility_name;
	}

	public function set_location_state($location_state) {
		$this->location_state = $location_state;
	}
	public function get_location_state() {
		return $this->location_state;
	}

	public function set_location_city($location_city) {
		$this->location_city = $location_city;
	}
	public function get_location_city() {
		return $this->location_city;
	}

	public function set_manager_employee_id($manager_employee_id) {
		$this->manager_employee_id = $manager_employee_id;
	}
	public function get_manager_employee_id() {
		return $this->manager_employee_id;
	}

	public function set_created_date($created_date) {
		$this->created_date = $created_date;
	}
	public function get_created_date() {
		return $this->created_date;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM facility WHERE $where";

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

	public static function populate($id = null, $facility_name = null, $location_state = null, $location_city = null, $manager_employee_id = null, $created_date = null) {
		$item = new facility();
		$item->set_id($id);
		$item->set_facility_name($facility_name);
		$item->set_location_state($location_state);
		$item->set_location_city($location_city);
		$item->set_manager_employee_id($manager_employee_id);
		$item->set_created_date($created_date);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_facility_name(),
			$this->get_location_state(),
			$this->get_location_city(),
			$this->get_manager_employee_id(),
			$this->get_created_date(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO facility(id, facility_name, location_state, location_city, manager_employee_id, created_date) VALUES (null, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE facility SET id = ?, facility_name = ?, location_state = ?, location_city = ?, manager_employee_id = ?, created_date = ? WHERE id = ?";
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
		$sql = "DELETE FROM facility WHERE id = ?";
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