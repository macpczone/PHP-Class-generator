<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardDB {

	private $id = null;
	private $admin_id = null;
	private $dashboard_type = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_admin_id($admin_id) {
		$this->admin_id = $admin_id;
	}
	public function get_admin_id() {
		return $this->admin_id;
	}

	public function set_dashboard_type($dashboard_type) {
		$this->dashboard_type = $dashboard_type;
	}
	public function get_dashboard_type() {
		return $this->dashboard_type;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM dashboard WHERE $where";

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

	public static function populate($id = null, $admin_id = null, $dashboard_type = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_admin_id($admin_id);
		$item->set_dashboard_type($dashboard_type);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_admin_id(),
			$this->get_dashboard_type(),
		);

		if ($this->get_id() == null) {
			unset($params[0]);
			$params = array_values($params);
			$sql = "INSERT INTO dashboard(id, admin_id, dashboard_type) VALUES (null, ?, ?)";
		} else {
			$sql = "UPDATE dashboard SET id = ?, admin_id = ?, dashboard_type = ? WHERE id = ?";
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
		$sql = "DELETE FROM dashboard WHERE id = ?";
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