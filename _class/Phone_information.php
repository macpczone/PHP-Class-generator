<?phpclass Phone_information {	private $id = null;	private $employee_id = null;	private $applicant_id = null;	private $phone_number = null;	private $extension = null;	private $phone_type = null;	public function set_id($id) {		$this->id = $id;	}	public function get_id() {		return $this->id;	}	public function set_employee_id($employee_id) {		$this->employee_id = $employee_id;	}	public function get_employee_id() {		return $this->employee_id;	}	public function set_applicant_id($applicant_id) {		$this->applicant_id = $applicant_id;	}	public function get_applicant_id() {		return $this->applicant_id;	}	public function set_phone_number($phone_number) {		$this->phone_number = $phone_number;	}	public function get_phone_number() {		return $this->phone_number;	}	public function set_extension($extension) {		$this->extension = $extension;	}	public function get_extension() {		return $this->extension;	}	public function set_phone_type($phone_type) {		$this->phone_type = $phone_type;	}	public function get_phone_type() {		return $this->phone_type;	}	public static function lookup($dbh, $where = 1, $params = array()) {		$sql = "SELECT id, employee_id, applicant_id, phone_number, extension, phone_type FROM phone_information WHERE $where";		$stmt = $dbh->prepare($sql);		$stmt->execute($params);		$results = $stmt->fetchAll(PDO::FETCH_CLASS, 'phone_information');		return $results;	}	public static function populate($id = null, $employee_id = null, $applicant_id = null, $phone_number = null, $extension = null, $phone_type = null) {		$item = new phone_information();		$item->set_id($id);		$item->set_employee_id($employee_id);		$item->set_applicant_id($applicant_id);		$item->set_phone_number($phone_number);		$item->set_extension($extension);		$item->set_phone_type($phone_type);		return $item;	}	public function write($dbh) {		$params = array(			$this->get_id(),			$this->get_employee_id(),			$this->get_applicant_id(),			$this->get_phone_number(),			$this->get_extension(),			$this->get_phone_type(),		);		if (!$this->id) {			$sql = "INSERT INTO phone_information (id, employee_id, applicant_id, phone_number, extension, phone_type) 
                    VALUES (?, ?, ?, ?, ?, ?)";		} else {			$sql = "UPDATE phone_information 
                    SET id = ?, employee_id = ?, applicant_id = ?, phone_number = ?, extension = ?, phone_type = ? 
                    WHERE id = ?";			$params[] = $this->id;		}		$stmt = $dbh->prepare($sql);		$stmt->execute($params);	}	public function delete($dbh) {		$params = array($this->id);		$sql = "DELETE FROM phone_information WHERE id = ?";		$stmt = $dbh->prepare($sql);		$stmt->execute($params);	}}