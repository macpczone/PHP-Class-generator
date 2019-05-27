<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class PermissionsDB {

	private $admin_id = null;
	private $permission = null;

	public function set_admin_id($admin_id) {
		$this->admin_id = $admin_id;
	}
	public function get_admin_id() {
		return $this->admin_id;
	}

	public function set_permission($permission) {
		$this->permission = $permission;
	}
	public function get_permission() {
		return $this->permission;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM permissions WHERE $where";

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

	public static function populate($admin_id = null, $permission = null) {
		$item = new permissions();
		$item->set_admin_id($admin_id);
		$item->set_permission($permission);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_admin_id(),
			$this->get_permission(),
		);

		if (!$this->$list_columns[0]) {
			$sql = "INSERT INTO permissions(admin_id, permission) VALUES (null, ?)";
		} else {
			$sql = "UPDATE permissions SET admin_id = ?, permission = ? WHERE admin_id = ?";
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
		$sql = "DELETE FROM permissions WHERE admin_id = ?";
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