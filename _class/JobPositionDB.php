<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobPositionDB {

	private $id = null;
	private $job_title = null;
	private $description = null;
	private $facility_id = null;
	private $created_date = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_job_title($job_title) {
		$this->job_title = $job_title;
	}
	public function get_job_title() {
		return $this->job_title;
	}

	public function set_description($description) {
		$this->description = $description;
	}
	public function get_description() {
		return $this->description;
	}

	public function set_facility_id($facility_id) {
		$this->facility_id = $facility_id;
	}
	public function get_facility_id() {
		return $this->facility_id;
	}

	public function set_created_date($created_date) {
		$this->created_date = $created_date;
	}
	public function get_created_date() {
		return $this->created_date;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM job_position WHERE $where";

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

	public static function populate($id = null, $job_title = null, $description = null, $facility_id = null, $created_date = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_job_title($job_title);
		$item->set_description($description);
		$item->set_facility_id($facility_id);
		$item->set_created_date($created_date);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_job_title(),
			$this->get_description(),
			$this->get_facility_id(),
			$this->get_created_date(),
		);

		if (!$this->id) {
			$sql = "INSERT INTO job_position(id, job_title, description, facility_id, created_date) VALUES (null, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE job_position SET id = ?, job_title = ?, description = ?, facility_id = ?, created_date = ? WHERE id = ?";
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
		$sql = "DELETE FROM job_position WHERE id = ?";
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