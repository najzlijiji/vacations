<?php
namespace Vacations;

class VacationStatus{
	const PENDING = 0;
	const APPROVED = 1;
	const REJECTED = -1;
	public static $statusName = [
		self::PENDING => 'pending',
		self::APPROVED => 'approved',
		self::REJECTED => 'rejected'
	];
}

?>