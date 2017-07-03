<?php

namespace Models;
use Vacations\Db;

class User{	

	/**
	 *
	 * @var Db $db
	 * @var string $error
	 */
	private $db;
	public $error='';

	public function __construct(){
		$this->db = Db::getinstance();
	}


	/**
	 *
	 * Get name and id of all users
	 * @return array|null 
	 */
	public function users(): ?array{
		$sql="SELECT id,name FROM users";
		$result=$this->db->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$data[]=$row;
		    }
		    return $data;
		} else {
		    return null;
		}
	}

	/**
	 *
	 * Get full info of one user
	 * @param int $userId
	 * @return array|null
	 */
	public function user(int $userId): ?array{
		$userId = $this->db->real_escape_string($userId);
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
	 * Add new user 
	 * @param string $name 
	 * @param int $days number of max vacation days
	 * @return bool
	 */
	public function adduser(string $name, int $days): bool{

		$name = $this->db->real_escape_string($name);
		$days = $this->db->real_escape_string($days);

		$check="SELECT id FROM users where name='$name'";
		$result=$this->db->query($check);
		if ($result->num_rows == 0) {
		    $sql="INSERT INTO users (name,vacation_days,remaining_days) VALUES ('$name','$days','$days')";
			if($this->db->query($sql) === TRUE){
				return true;
			}
			else{
				return false;
			}
		} else {
			$this->error = "Name needs to be unique";
		    return false;
		}		
	}


	/**
	 * Set remaining vacation days for user
	 * @param int $userId 
	 * @return bool
	 */
	public function setRemainingDays(int $userId, int $remainingDays): bool{

		$userId = $this->db->real_escape_string($userId);
		$remainingDays = $this->db->real_escape_string($remainingDays);

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