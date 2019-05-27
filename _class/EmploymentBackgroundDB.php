<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmploymentBackgroundDB {

	private $id = null;
	private $applicant_id = null;
	private $employee_id = null;
	private $from_date = null;
	private $to_date = null;
	private $institution = null;
	private $job_title = null;
	private $reason_for_leaving = null;
	private $supervisor_name = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_applicant_id($applicant_id) {
		$this->applicant_id = $applicant_id;
	}
	public function get_applicant_id() {
		return $this->applicant_id;
	}

	public function set_employee_id($employee_id) {
		$this->employee_id = $employee_id;
	}
	public function get_employee_id() {
		return $this->employee_id;
	}

	public function set_from_date($from_date) {
		$this->from_date = $from_date;
	}
	public function get_from_date() {
		return $this->from_date;
	}

	public function set_to_date($to_date) {
		$this->to_date = $to_date;
	}
	public function get_to_date() {
		return $this->to_date;
	}

	public function set_institution($institution) {
		$this->institution = $institution;
	}
	public function get_institution() {
		return $this->institution;
	}

	public function set_job_title($job_title) {
		$this->job_title = $job_title;
	}
	public function get_job_title() {
		return $this->job_title;
	}

	public function set_reason_for_leaving($reason_for_leaving) {
		$this->reason_for_leaving = $reason_for_leaving;
	}
	public function get_reason_for_leaving() {
		return $this->reason_for_leaving;
	}

	public function set_supervisor_name($supervisor_name) {
		$this->supervisor_name = $supervisor_name;
	}
	public function get_supervisor_name() {
		return $this->supervisor_name;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM employment_background WHERE $where";

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

	public static function populate($id = null, $applicant_id = null, $employee_id = null, $from_date = null, $to_date = null, $institution = null, $job_title = null, $reason_for_leaving = null, $supervisor_name = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_applicant_id($applicant_id);
		$item->set_employee_id($employee_id);
		$item->set_from_date($from_date);
		$item->set_to_date($to_date);
		$item->set_institution($institution);
		$item->set_job_title($job_title);
		$item->set_reason_for_leaving($reason_for_leaving);
		$item->set_supervisor_name($supervisor_name);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_applicant_id(),
			$this->get_employee_id(),
			$this->get_from_date(),
			$this->get_to_date(),
			$this->get_institution(),
			$this->get_job_title(),
			$this->get_reason_for_leaving(),
			$this->get_supervisor_name(),
		);

		if (!$this->id) {
			$sql = "INSERT INTO employment_background(id, applicant_id, employee_id, from_date, to_date, institution, job_title, reason_for_leaving, supervisor_name) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE employment_background SET id = ?, applicant_id = ?, employee_id = ?, from_date = ?, to_date = ?, institution = ?, job_title = ?, reason_for_leaving = ?, supervisor_name = ? WHERE id = ?";
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
		$sql = "DELETE FROM employment_background WHERE id = ?";
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