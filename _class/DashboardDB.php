<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class DashboardDB {

	private $admin_id = null;
	private $dashboard_type = null;

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
		} catch(PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}

		return $sth->fetchAll(PDO::FETCH_CLASS, get_class());
	}

	public static function populate($admin_id = null, $dashboard_type = null) {
		$item = new dashboard();
		$item->set_admin_id($admin_id);
		$item->set_dashboard_type($dashboard_type);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_admin_id(),
			$this->get_dashboard_type(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO dashboard(admin_id, dashboard_type) VALUES (null, ?)";
		} else {
			$sql = "UPDATE dashboard SET admin_id = ?, dashboard_type = ? WHERE admin_id = ?";
			$params[] = $this->admin_id;
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
		$sql = "DELETE FROM dashboard WHERE admin_id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->admin_id));
		} catch(PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}