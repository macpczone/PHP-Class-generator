<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class JobsAppliedDB {

	private $job_id = null;
	private $applicant_id = null;
	private $application_date = null;
	private $status = null;

	public function set_job_id($job_id) {
		$this->job_id = $job_id;
	}
	public function get_job_id() {
		return $this->job_id;
	}

	public function set_applicant_id($applicant_id) {
		$this->applicant_id = $applicant_id;
	}
	public function get_applicant_id() {
		return $this->applicant_id;
	}

	public function set_application_date($application_date) {
		$this->application_date = $application_date;
	}
	public function get_application_date() {
		return $this->application_date;
	}

	public function set_status($status) {
		$this->status = $status;
	}
	public function get_status() {
		return $this->status;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM jobs_applied WHERE $where";

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

	public static function populate($job_id = null, $applicant_id = null, $application_date = null, $status = null) {
		$item = new jobs_applied();
		$item->set_job_id($job_id);
		$item->set_applicant_id($applicant_id);
		$item->set_application_date($application_date);
		$item->set_status($status);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_job_id(),
			$this->get_applicant_id(),
			$this->get_application_date(),
			$this->get_status(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO jobs_applied(job_id, applicant_id, application_date, status) VALUES (null, ?, ?, ?)";
		} else {
			$sql = "UPDATE jobs_applied SET job_id = ?, applicant_id = ?, application_date = ?, status = ? WHERE job_id = ?";
			$params[] = $this->job_id;
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
		$sql = "DELETE FROM jobs_applied WHERE job_id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->job_id));
		} catch(PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}