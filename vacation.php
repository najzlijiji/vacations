<?php


interface vacationModule{

	public function requestVacation($userId,$start_date,$end_date);
	public function vacationRequests();
	public function approvedVacations();
	public function rejectedVacations();
	public function cancelVacation($vacationId,$userId);
	public function approveVacation($vacationId,$userId);
	public function rejectVacation($vacationId);
	public function initFilter($from,$to);
	public function remainingDays($userId);
	public function vacationDetails($vacationId);
	public function vacationDays($start_date,$end_date);
	public function overlapCheck($userId,$start_date,$end_date);
	public function user($userId);
	public function users();
	public function setRemainingDays($userId);

}

class vacation implements vacationModule{

	/**
	 *
	 * @var string $dbPassword database password
	 * @var string $dbUsername database username
	 * @var string $dbHost database hostname
	 * @var string $dbName database name
	 * @var string $db placeholder for mysqli object
	 */
	private $dbPassword='redstar';
	private $dbUsername='root';
	private $dbHost='localhost';
	private $dbName='vacation';
	private $db;
	
	public $numPending='';
	public $numApproved='';
	public $numRejected='';
	public $filter;
	const PENDING=0;
	const APPROVED=1;
	const REJECTED=-1;

	public function __construct(){
		$this->db = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
	}

	public function __destruct(){
		$this->db->close();
	}


	/**
	 *
	 * @param int $userId
	 * @param string $start_date needs to validate to 'Y-m-d'
	 * @param string $end_date needs to validate to 'Y-m-d'
	 */
	public function requestVacation($userId,$start_date,$end_date){
		if(is_date($start_date) && is_date($end_date) && is_int($userId)){
			if($this->overlapCheck($userId,$start_date,$end_date)){
				$total_days=$this->vacationDays($start_date,$end_date);

				$requested=date('Y-m-d');
				$sql="INSERT INTO vacations (user_id, start_date, end_date, total_days, approved, requested) VALUES ($userId, '$start_date', '$end_date', '$total_days', ". self::PENDING .", NOW())";
				if($this->db->query($sql) === TRUE){
					$this->numVacations();
					return true;
				}
				else{
					var_dump($this->db->error());
					return false;
				}
			}
			else{
				return "You can't request vacation on overlapping dates";
			}

		}
		return "bad parameters";
	}

	/**
	 *
	 * @param int $page LIMIT offset value 
	 */
	public function vacationRequests($page=0){
		$this->numVacations();	
		$vac="SELECT vacations.*,users.name,users.vacation_days,users.remaining_days FROM vacations JOIN users ON vacations.user_id=users.id WHERE vacations.deleted=0 AND vacations.approved=". self::PENDING . $this->filter." ORDER BY vacations.requested,vacations.id DESC LIMIT $page,10";
		$result=$this->db->query($vac);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data[]=$row;
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param int $page LIMIT offset value 
	 */
	public function approvedVacations($page=0){
		$this->numVacations();
		$sql="SELECT vacations.*,users.name,users.vacation_days,users.remaining_days FROM vacations JOIN users ON vacations.user_id=users.id  WHERE vacations.deleted=0 AND vacations.approved=". self::APPROVED .$this->filter. " ORDER BY vacations.requested,vacations.id DESC LIMIT $page,10";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data[]=$row;
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param int $page LIMIT offset value 
	 */
	public function rejectedVacations($page=0){
		$this->numVacations();
		$sql="SELECT vacations.*,users.name,users.vacation_days,users.remaining_days FROM vacations JOIN users ON vacations.user_id=users.id  WHERE vacations.deleted=0 AND vacations.approved=". self::REJECTED .$this->filter. " ORDER BY vacations.requested,vacations.id DESC LIMIT $page,10";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data[]=$row;
		    }
		    return $data;
		} else {
		    return false;
		}		
	}

	/**
	 *
	 * @param int $vacationId
	 * @param int $userId
	 */
	public function cancelVacation($vacationId,$userId){
		if(is_int($vacationId)){
			$sql="UPDATE vacations SET deleted=unix_timestamp() WHERE id='$vacationId'";
			if($this->db->query($sql) === TRUE){
				$this->numVacations();
				$this->setRemainingDays($userId);
				return true;
			}
			else{
				return false;
			}
		}
		return false;
	}

	/**
	 *
	 * @param int $vacationId
	 * @param int $userId
	 */	
	public function approveVacation($vacationId,$userId){
		if(is_int($vacationId)){
			$user = $this->user($userId);
			$vacation = $this->vacationDetails($vacationId);
			$total_vacation_days = $this->vacationDays($vacation['start_date'],$vacation['end_date']);
			if($this->overlapCheck($userId,$vacation['start_date'],$vacation['end_date'])){
				if($total_vacation_days<=$user['remaining_days']){
					$sql="UPDATE vacations SET approved=". self::APPROVED ." WHERE id='$vacationId'";
					if($this->db->query($sql) === TRUE){
						$this->numVacations();
						$this->setRemainingDays($userId);
						return true;
					}
					else{
						return false;
					}
				}
				else{
					return "You reached maximum vacation amount for this year";
				}
			}
			else{
				return "You cannot have vacation on overlapping dates";
			}
		}
		return false;
	}

	/**
	 *
	 * @param int $vacationId 
	 */
	public function rejectVacation($vacationId){
		if(is_int($vacationId)){
			$sql="UPDATE vacations SET approved=". self::REJECTED ." WHERE id='$vacationId'";
			if($this->db->query($sql) === TRUE){
				$this->numVacations();
				return true;
			}
			else{
				return false;
			}			
		}
		return false;
	}

	public function numVacations(){
		$pending="SELECT count(id) as cnt FROM vacations WHERE deleted=0 AND approved=". self::PENDING . $this->filter;
		$approved="SELECT count(id) as cnt FROM vacations WHERE deleted=0 AND approved=". self::APPROVED . $this->filter;
		$rejected="SELECT count(id) as cnt FROM vacations WHERE deleted=0 AND approved=". self::REJECTED . $this->filter;
		$result=$this->db->query($pending);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$pdata=$row['cnt'];
		    }
		    $this->numPending=$pdata;
		} else {
		    return false;
		}
		$result=$this->db->query($approved);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$adata=$row['cnt'];
		    }
		    $this->numApproved=$adata;
		} else {
		    return false;
		}
		$result=$this->db->query($rejected);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$rdata=$row['cnt'];
		    }
		    $this->numRejected=$rdata;
		} else {
		    return false;
		}
		return false;
	}

	/**
	 *
	 * @param string $from needs to validate to 'Y-m-d'
	 * @param string $to needs to validate to 'Y-m-d'
	 */
	public function initFilter($from,$to){
		if(is_date($from) && is_date($to)){
			$this->filter=" AND vacations.start_date>='$from' AND vacations.start_date<='$to'";
		}
		else{
			$this->filter='';
		}
	}

	/**
	 *
	 * @param int $userId 
	 */
	public function remainingDays($userId){
		$from=date('Y-01-01');
		$to=date('Y-01-01', strtotime('+1 year'));
		$sql="SELECT users.vacation_days-SUM(temp.total) as total FROM (SELECT id,user_id,start_date,end_date,total_days, 
				CASE 
					WHEN (start_date>='$from' AND end_date<'$to') THEN DATEDIFF(end_date,start_date)+1
					WHEN (start_date>='$from' AND start_date<'$to' AND end_date>='$from') THEN DATEDIFF('$to',start_date)
				END as total 
				FROM vacations WHERE 
				start_date>='$from' AND
				start_date<'2$to' AND
				deleted=0 AND 
				user_id='$userId' AND
				approved=".self::APPROVED."
				ORDER BY id DESC) as temp
				JOIN users on temp.user_id=users.id 
				WHERE temp.user_id='$userId'";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data=$row['total'];
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param int $vacationId 
	 */
	public function vacationDetails($vacationId){
		$sql="SELECT * FROM vacations WHERE id='$vacationId'";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data=$row;
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param string $start_date needs to validate to 'Y-m-d'
	 * @param string $end_date needs to validate to 'Y-m-d'
	 */
	public function vacationDays($start_date,$end_date){
		$dStart = new DateTime($end_date);
		$dEnd  = new DateTime($start_date);
		$dDiff = $dStart->diff($dEnd);
		return ($dDiff->days)+1;
	}

	/**
	 *
	 * @param int $userId
	 */
	public function user($userId){
		$sql="SELECT * FROM users WHERE id='$userId'";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data=$row;
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	public function users(){
		$sql="SELECT id,name FROM users";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data[]=$row;
		    }
		    return $data;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param string $name 
	 * @param int $days number of max vacation days
	 */
	public function adduser($name,$days){
		$check="SELECT id FROM users where name='$name'";
		$result=$this->db->query($check);
		if ($result->num_rows == 0) {
		    $sql="INSERT INTO users (name,vacation_days,remaining_days) VALUES ('$name','$days','$days')";
			if($this->db->query($sql) === TRUE){
				return true;
			}
			else{
				var_dump($this->db->error());
				return false;
			}
		} else {
		    return "Name needs to be unique";
		}
		
	}

	/**
	 *
	 * @param int $userId 
	 * @param string $start_date needs to validate to 'Y-m-d'
	 * @param string $end_date needs to validate to 'Y-m-d'
	 */
	public function overlapCheck($userId,$start_date,$end_date){
		$sql="SELECT * FROM vacations WHERE user_id='$userId' AND deleted=0 AND approved='". self::APPROVED ."' AND ((start_date BETWEEN '$start_date' AND '$end_date') OR (end_date BETWEEN '$start_date' AND '$end_date'))";
		$result=$this->db->query($sql);
		if ($result->num_rows == 0) {
		    return true;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * @param int $userId 
	 */
	public function setRemainingDays($userId){
		$remainingDays= $this->remainingDays($userId);
		$sql="UPDATE users SET remaining_days='$remainingDays' WHERE id='$userId'";
			if($this->db->query($sql) === TRUE){
				return true;
			}
			else{
				return false;
			}			
		}
}

function is_date($date) {
    return (date('Y-m-d', strtotime($date)) == $date);
}

?>


