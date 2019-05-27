<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class QualificationDB {

	private $id = null;
	private $employee_id = null;
	private $applicant_id = null;
	private $degree_cert = null;
	private $certification_id = null;
	private $effective_date = null;
	private $expiration_date = null;

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

	public function set_degree_cert($degree_cert) {
		$this->degree_cert = $degree_cert;
	}
	public function get_degree_cert() {
		return $this->degree_cert;
	}

	public function set_certification_id($certification_id) {
		$this->certification_id = $certification_id;
	}
	public function get_certification_id() {
		return $this->certification_id;
	}

	public function set_effective_date($effective_date) {
		$this->effective_date = $effective_date;
	}
	public function get_effective_date() {
		return $this->effective_date;
	}

	public function set_expiration_date($expiration_date) {
		$this->expiration_date = $expiration_date;
	}
	public function get_expiration_date() {
		return $this->expiration_date;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM qualification WHERE $where";

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

	public static function populate($id = null, $employee_id = null, $applicant_id = null, $degree_cert = null, $certification_id = null, $effective_date = null, $expiration_date = null) {
		$item = new qualification();
		$item->set_id($id);
		$item->set_employee_id($employee_id);
		$item->set_applicant_id($applicant_id);
		$item->set_degree_cert($degree_cert);
		$item->set_certification_id($certification_id);
		$item->set_effective_date($effective_date);
		$item->set_expiration_date($expiration_date);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_employee_id(),
			$this->get_applicant_id(),
			$this->get_degree_cert(),
			$this->get_certification_id(),
			$this->get_effective_date(),
			$this->get_expiration_date(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO qualification(id, employee_id, applicant_id, degree_cert, certification_id, effective_date, expiration_date) VALUES (null, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE qualification SET id = ?, employee_id = ?, applicant_id = ?, degree_cert = ?, certification_id = ?, effective_date = ?, expiration_date = ? WHERE id = ?";
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
		$sql = "DELETE FROM qualification WHERE id = ?";
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