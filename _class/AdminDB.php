<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDB {

	private $id = null;
	private $employee_id = null;
	private $password = null;
	private $status = null;
	private $created_date = null;
	private $test_account = null;
	private $selector = null;
	private $token = null;
	private $expires = null;

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

	public function set_password($password) {
		$this->password = $password;
	}
	public function get_password() {
		return $this->password;
	}

	public function set_status($status) {
		$this->status = $status;
	}
	public function get_status() {
		return $this->status;
	}

	public function set_created_date($created_date) {
		$this->created_date = $created_date;
	}
	public function get_created_date() {
		return $this->created_date;
	}

	public function set_test_account($test_account) {
		$this->test_account = $test_account;
	}
	public function get_test_account() {
		return $this->test_account;
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
		$sql = "SELECT * FROM admin WHERE $where";

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

	public static function populate($id = null, $employee_id = null, $password = null, $status = null, $created_date = null, $test_account = null, $selector = null, $token = null, $expires = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_employee_id($employee_id);
		$item->set_password($password);
		$item->set_status($status);
		$item->set_created_date($created_date);
		$item->set_test_account($test_account);
		$item->set_selector($selector);
		$item->set_token($token);
		$item->set_expires($expires);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_employee_id(),
			$this->get_password(),
			$this->get_status(),
			$this->get_created_date(),
			$this->get_test_account(),
			$this->get_selector(),
			$this->get_token(),
			$this->get_expires(),
		);

		if ($this->get_id() == null) {
			unset($params[0]);
			$params = array_values($params);
			$sql = "INSERT INTO admin(id, employee_id, password, status, created_date, test_account, selector, token, expires) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE admin SET id = ?, employee_id = ?, password = ?, status = ?, created_date = ?, test_account = ?, selector = ?, token = ?, expires = ? WHERE id = ?";
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
		$sql = "DELETE FROM admin WHERE id = ?";
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