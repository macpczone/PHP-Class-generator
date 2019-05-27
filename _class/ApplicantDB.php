<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicantDB {

	private $id = null;
	private $first_name = null;
	private $last_name = null;
	private $mid_init = null;
	private $email = null;
	private $date_of_birth = null;
	private $ssn = null;
	private $created_date = null;
	private $password = null;
	private $selector = null;
	private $token = null;
	private $expires = null;

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

	public function set_last_name($last_name) {
		$this->last_name = $last_name;
	}
	public function get_last_name() {
		return $this->last_name;
	}

	public function set_mid_init($mid_init) {
		$this->mid_init = $mid_init;
	}
	public function get_mid_init() {
		return $this->mid_init;
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

	public function set_password($password) {
		$this->password = $password;
	}
	public function get_password() {
		return $this->password;
	}

	public function set_selector($selector) {
		$this->selector = $selector;
	}
	public function get_selector() {
		return $this->selector;
	}

	public function set_token($token) {
		$this->token = $token;
	}
	public function get_token() {
		return $this->token;
	}

	public function set_expires($expires) {
		$this->expires = $expires;
	}
	public function get_expires() {
		return $this->expires;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM applicant WHERE $where";

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

	public static function populate($id = null, $first_name = null, $last_name = null, $mid_init = null, $email = null, $date_of_birth = null, $ssn = null, $created_date = null, $password = null, $selector = null, $token = null, $expires = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_first_name($first_name);
		$item->set_last_name($last_name);
		$item->set_mid_init($mid_init);
		$item->set_email($email);
		$item->set_date_of_birth($date_of_birth);
		$item->set_ssn($ssn);
		$item->set_created_date($created_date);
		$item->set_password($password);
		$item->set_selector($selector);
		$item->set_token($token);
		$item->set_expires($expires);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_first_name(),
			$this->get_last_name(),
			$this->get_mid_init(),
			$this->get_email(),
			$this->get_date_of_birth(),
			$this->get_ssn(),
			$this->get_created_date(),
			$this->get_password(),
			$this->get_selector(),
			$this->get_token(),
			$this->get_expires(),
		);

		if (!$this->id) {
			$sql = "INSERT INTO applicant(id, first_name, last_name, mid_init, email, date_of_birth, ssn, created_date, password, selector, token, expires) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE applicant SET id = ?, first_name = ?, last_name = ?, mid_init = ?, email = ?, date_of_birth = ?, ssn = ?, created_date = ?, password = ?, selector = ?, token = ?, expires = ? WHERE id = ?";
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
		$sql = "DELETE FROM applicant WHERE id = ?";
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