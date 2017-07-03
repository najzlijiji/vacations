CREATE DATABASE IF NOT EXISTS `vacation`;
USE `vacation`;

CREATE TABLE `vacations`.`users` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR(255) NOT NULL ,
	`vacation_days` INT NOT NULL ,
	`remaining_days` INT NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `vacations`.`vacations` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`user_id` INT NOT NULL ,
	`start_date` DATE NOT NULL ,
	`end_date` DATE NOT NULL ,
	`total_days` INT NOT NULL ,
	`approved` TINYINT NOT NULL ,
	`deleted` INT NOT NULL ,
	`requested` DATE NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;