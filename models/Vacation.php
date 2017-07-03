<?php

namespace Models;
use Vacations\Db;
use Vacations\VacationStatus;
use Models\User;

class Vacation{

	/**
	 *
	 * @var Db $db
	 * @var string $error
	 * @var string $filter
	 * @var int $offset
	 * @var int $remainingDays
	 * @var array $data
	 * @var array $pages
	 * @var array $vacationInfo
	 */
	private $db;
	public $error='';
	public $filter='';
	public $offset=0;
	public $remainingDays;
	public $data=[];
	public $pages=[];
	private $vacationInfo=[];


	public function __construct(){
		$this->db = Db::getinstance();
	}

	public function __destruct(){
		$this->db->close();
	}

	/**
	 *
	 * Returns all vacations of specified status
	 * @param int $status
	 * @return bool
	 */
	public function returnVacations(int $status): bool{
		$this->numVacations();	
		$sql="SELECT vacations.*,users.name,users.vacation_days,users.remaining_days FROM vacations JOIN users ON vacations.user_id=users.id WHERE vacations.deleted=0 AND vacations.status=$status " . $this->filter ." ORDER BY vacations.requested,vacations.id DESC LIMIT ".$this->offset.",10";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$data[]=$row;
			}
			$this->data=$data;
			return true;
		}
		else{
			return false;	
		}
	}

	/**
	 *
	 * Aprove Vacation
	 * @param int $vacationId
	 * @param int $userId
	 * @return bool
	 */	
	public function approveVacation(int $vacationId, int $userId): bool{
		$vacationId = $this->db->real_escape_string($vacationId);
		$userId = $this->db->real_escape_string($userId);
		$user= new User;
		$userInfo = $user->user($userId);
		$this->vacationDetails($vacationId);
		$vacation = $this->vacationInfo;
		$total_vacation_days = $this->vacationDays($vacation['start_date'],$vacation['end_date']);
		if($this->overlapCheck($userId,$vacation['start_date'],$vacation['end_date'])){
			if($total_vacation_days<=$userInfo['remaining_days']){
				$sql="UPDATE vacations SET status=". VacationStatus::APPROVED ." WHERE id='$vacationId'";
				if($this->db->query($sql) === TRUE){
					$this->numVacations();
					if($this->remainingDays($userId)){
						$remainingDays = $this->remainingDays;
						$user->setRemainingDays($userId,$remainingDays);					
					}
					return true;
				}
				else{
					$this->error = "Error! Unable to update database";
					return false;
				}
			}
			else{
				$this->error = "You reached maximum vacation amount for this year";
				return false;
			}
		}
		else{
			$this->error = "You cannot have vacation on overlapping dates";
			return false;
		}
	}

	/**
	 *
	 * Reject Vacation
	 * @param int $vacationId 
	 * @return bool
	 */
	public function rejectVacation(int $vacationId): bool{
		$vacationId = $this->db->real_escape_string($vacationId);
		$sql="UPDATE vacations SET status='". VacationStatus::REJECTED ."' WHERE id='$vacationId'";
		if($this->db->query($sql) === TRUE){
			$this->numVacations();
			return true;
		}
		else{
			$this->error = "Error! Unable to update database";
			return false;
		}			
	}


	/**
	 * 
	 * calculates total number of vacations in database for each state
	 */

	public function numVacations(){
		$sql="SELECT status,count(id) as cnt FROM vacations WHERE deleted=0" . $this->filter ." GROUP BY status";
		$result = $this->db->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cnt[$row['status']]=$row['cnt'];
			}
			if(array_key_exists(VacationStatus::PENDING,$cnt)){
				$this->pages[VacationStatus::PENDING]=$cnt[VacationStatus::PENDING];
			}
			else{
				$this->pages[VacationStatus::PENDING]=0;
			}
			if(array_key_exists(VacationStatus::APPROVED,$cnt)){
				$this->pages[VacationStatus::APPROVED]=$cnt[VacationStatus::APPROVED];
			}
			else{
				$this->pages[VacationStatus::APPROVED]=0;
			}
			if(array_key_exists(VacationStatus::REJECTED,$cnt)){
				$this->pages[VacationStatus::REJECTED]=$cnt[VacationStatus::REJECTED];
			}
			else{
				$this->pages[VacationStatus::REJECTED]=0;
			}
		}

	}

	/**
	 *
	 * Request Vacation
	 * @param int $userId
	 * @param string $startDate needs to validate to date
	 * @param string $endDate needs to validate to date
	 * @return bool
	 */
	public function requestVacation(int $userId, string $startDate, string $endDate): bool {
		$userId = $this->db->real_escape_string($userId);
		$startDate = $this->db->real_escape_string($startDate);
		$endDate = $this->db->real_escape_string($endDate);
		if(isDate($startDate) && isDate($endDate)){
			if($this->overlapCheck($userId,$startDate,$endDate)){
				$total_days=$this->vacationDays($startDate,$endDate);
				$requested=date('Y-m-d');
				$sql="INSERT INTO vacations (user_id, start_date, end_date, total_days, status, requested) VALUES ($userId, '$startDate', '$endDate', '$total_days', ". VacationStatus::PENDING .", NOW())";
				if($this->db->query($sql) === TRUE){
					$this->numVacations();
					return true;
				}
				else{
					$this->error = 'Error! Unable to write to database';
					return false;
				}
			}
			else{
				$this->error =  "You can't request vacation on overlapping dates";
				return false;
			}
		}
		$this->error =  "Error! Bad Parameters";
		return false;
	}

	/**
	 *
	 * Cancel Vacation
	 * @param int $vacationId
	 * @param int $userId
	 * @return bool
	 */
	public function cancelVacation(int $vacationId, int $userId): bool{
		$vacationId = $this->db->real_escape_string($vacationId);
		$userId = $this->db->real_escape_string($userId);
		$user = new User;
		$sql="UPDATE vacations SET deleted=unix_timestamp() WHERE id='$vacationId'";
		if($this->db->query($sql) === TRUE){
			$this->numVacations();
			if($this->remainingDays($userId)){
				$remainingDays = $this->remainingDays;
				$user->setRemainingDays($userId,$remainingDays);					
			}
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 *
	 * Get Vacation details
	 * @param int $vacationId
	 * @return bool
	 */
	public function vacationDetails(int $vacationId): bool{
		$sql="SELECT * FROM vacations WHERE id='$vacationId'";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$this->vacationInfo = $row;
		    }
		    return true;
		} else {
		    return false;
		}
	}

	/**
	 *
	 * Check if dates overlap
	 * @param int $userId 
	 * @param string $startDate needs to validate to date
	 * @param string $endDate needs to validate to date
	 * @return bool
	 */
	public function overlapCheck(int $userId, string $startDate, string $endDate): bool{
		if(isDate($startDate) && isDate($endDate)){
			$sql="SELECT * FROM vacations WHERE user_id='$userId' AND deleted=0 AND status='". VacationStatus::APPROVED ."' AND start_date < '$endDate'AND end_date > '$startDate'";
			$result=$this->db->query($sql);
			if ($result->num_rows == 0) {
			    return true;
			} else {
			    return false;
			}
		}
	}

	/**
	 *
	 * Calculate remaining days of Vacation for $userId
	 * @param int $userId 
	 * @return bool
	 */	
	public function remainingDays(int $userId): bool{
		$userId = $this->db->real_escape_string($userId);

		$date = new \DateTime();
		$from = $date->format('Y-01-01'); // start of current year

		$date->add(new \DateInterval('P1Y'));
		$to = $date->format('Y-01-01'); // start of next year

		$user= new User;
		$userInfo = $user->user($userId);
		$total = $userInfo['vacation_days'];
		$sql="SELECT start_date, end_date FROM vacations WHERE user_id='$userId' AND deleted=0 AND status=".VacationStatus::APPROVED." AND start_date>='$from' AND start_date<'$to'";
		$result=$this->db->query($sql);
		if ($result && $result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$start_date = new \DateTime($row['start_date']);
				$end_date = new \DateTime($row['end_date']);
				if($end_date>=$date){
					$diffDays = $start_date->diff($date);
					$total-= $diffDays->days+1;
				}
				else{
					$diffDays = $start_date->diff($end_date);
					$total-= $diffDays->days+1;
				}
			}
			$this->remainingDays = $total;
		    return true;
		} 
		else {
		    return false;
		}
	}

	/**
	 *
	 * Calculate total Vacation days
	 * @param string $startDate
	 * @param string $endDate
	 * @return int
	 */
	public function vacationDays(string $startDate, string $endDate): int{
		$dStart = new \DateTime($startDate);
		$dEnd  = new \DateTime($endDate);
		$dDiff = $dStart->diff($dEnd);
		return ($dDiff->days)+1;
	}



	/**
	 *
	 * Prepare filter variable
	 * @param array $filter
	 */
	public function initFilter(array $filter){
		if(!empty($filter)){			
			$from=$filter[0];
			$to=$filter[1];
			if(isDate($from) && isDate($to)){
				$this->filter=" AND vacations.start_date>='$from' AND vacations.start_date<='$to'";
			}
		}
	}
}
?>