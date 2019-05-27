<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobsAssignmentDB {

	private $job_id = null;
	private $employee_id = null;
	private $assignment_date = null;

	public function set_job_id($job_id) {
		$this->job_id = $job_id;
	}
	public function get_job_id() {
		return $this->job_id;
	}

	public function set_employee_id($employee_id) {
		$this->employee_id = $employee_id;
	}
	public function get_employee_id() {
		return $this->employee_id;
	}

	public function set_assignment_date($assignment_date) {
		$this->assignment_date = $assignment_date;
	}
	public function get_assignment_date() {
		return $this->assignment_date;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM jobs_assignment WHERE $where";

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

	public static function populate($job_id = null, $employee_id = null, $assignment_date = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_job_id($job_id);
		$item->set_employee_id($employee_id);
		$item->set_assignment_date($assignment_date);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_job_id(),
			$this->get_employee_id(),
			$this->get_assignment_date(),
		);

		if (!$this->job_id) {
			$sql = "INSERT INTO jobs_assignment(job_id, employee_id, assignment_date) VALUES (null, ?, ?)";
		} else {
			$sql = "UPDATE jobs_assignment SET job_id = ?, employee_id = ?, assignment_date = ? WHERE job_id = ?";
			$params[] = $this->job_id;
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
		$sql = "DELETE FROM jobs_assignment WHERE job_id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->job_id));
		} catch(\PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}