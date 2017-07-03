<?php

namespace Models;
use Vacations\Db;

class User{	

	private $db;
	public $name;
	public $vacationDays;
	public $remainingDays;

	public function __construct(){
		$this->db = Db::getinstance();
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
		    return null;
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
	 */
	public function setRemainingDays($userId,$remainingDays){		
		$sql="UPDATE users SET remaining_days='$remainingDays' WHERE id='$userId'";
			if($this->db->query($sql) === TRUE){
				return true;
			}
			else{
				return false;
			}			
		}

}
?>