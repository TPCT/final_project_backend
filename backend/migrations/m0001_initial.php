<?php

namespace migrations;

use core\Application;
use Exception;

class m0001_initial
{
    public function up(): bool
    {
        $connector = Application::APP()->database->connector();
        try {
            $query = "CREATE TABLE IF NOT EXISTS `users`(
                            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `username` VARCHAR(20) NOT NULL UNIQUE,
                            `password` VARCHAR(127) NOT NULL,
                            `mac_address` VARCHAR(127) NOT NULL UNIQUE,
                            `privileges` TINYINT NOT NULL,
                            `creation_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
                            `last_access_time` DATETIME DEFAULT NULL
                        ) ENGINE = INNODB;
            ";

            $stmt = $connector->prepare($query);
            $status = $stmt->execute();

            if (!$status)
                return False;

            $query = "CREATE TABLE IF NOT EXISTS `train_event`(
                            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `train_id` BIGINT UNSIGNED NOT NULL,
                            `event` TEXT NOT NULL,
                            `emergent` BINARY NOT NULL DEFAULT 0,
                            `creation_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            CONSTRAINT train_event_train_id_fk FOREIGN KEY(`train_id`) 
                                REFERENCES `trains_system`.`users`(`id`) 
                                ON DELETE CASCADE 
                                ON UPDATE CASCADE
                        ) ENGINE = INNODB";
            $stmt = $connector->prepare($query);
            $status = $stmt->execute();

            if (!$status)
                return False;

            $query = "CREATE TABLE IF NOT EXISTS `user_event`( 
                            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `event` TEXT NOT NULL,
                            `user_id` BIGINT UNSIGNED NOT NULL,
                            `target_id` BIGINT UNSIGNED NOT NULL,
                            `creation_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
                            CONSTRAINT user_event_target_fk FOREIGN KEY(`target_id`) 
                                REFERENCES `trains_system`.`users`(`id`) 
                                ON DELETE CASCADE 
                                ON UPDATE CASCADE,
                            CONSTRAINT user_event_user_fk FOREIGN KEY(`user_id`) 
                                REFERENCES `trains_system`.`users`(`id`) 
                                ON DELETE CASCADE 
                                ON UPDATE CASCADE
                        ) ENGINE = InnoDB;";
            $stmt = $connector->prepare($query);
            $status = $stmt->execute();

            if (!$status)
                return False;

            $query = "CREATE TABLE IF NOT EXISTS `emergency_action`( 
                            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `fields` TEXT NOT NULL,
                            `action` BIGINT UNSIGNED NOT NULL,
                            `user_id` BIGINT UNSIGNED NOT NULL, 
                            `creation_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
                            CONSTRAINT emergency_action_creator_fk FOREIGN KEY(`user_id`) 
                                REFERENCES `trains_system`.`users`(`id`) 
                                ON DELETE CASCADE 
                                ON UPDATE CASCADE 
                        ) ENGINE = InnoDB;";
            $stmt = $connector->prepare($query);
            $status = $stmt->execute();

        } catch (Exception $e) {
            Application::APP()->error_logger->log(0, $e, __FILE__, __LINE__);
            $status = False;
        }
        return $status;
    }

    public function down()
    {
        $connector = Application::APP()->database->connector();
        $connector->exec("DROP TABLE IF EXISTS `trains_system`.`users`");
        $connector->exec("DROP TABLE IF EXISTS `trains_system`.`train_event`");
        $connector->exec("DROP TABLE IF EXISTS `trains_system`.`user_event`");
        $connector->exec("DROP TABLE IF EXISTS `trains_system`.`emergency_action`");
        $connector->exec("DROP TABLE IF EXISTS `trains_system`.`emergency_event`");
    }
}
