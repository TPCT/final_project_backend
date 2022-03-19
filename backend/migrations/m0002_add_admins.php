<?php

namespace migrations;

use core\Application;
use Exception;

class m0002_add_admins
{
    public function up(): bool
    {
        $connector = Application::APP()->database->connector();
        try {
            $connector->beginTransaction();

            $query = "INSERT INTO `trains_system`.`users` (username, password, privileges, mac_address) VALUES (
                            :username, :password, :privileges, :mac_address                                                              
                        )";

            $stmt = $connector->prepare($query);
            $username = "admin";
            $password = password_hash("admin", PASSWORD_DEFAULT);
            $mac_address = "FF:FF:FF:FF:FF:FF";
            $privileges = 2;

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":mac_address", $mac_address);
            $stmt->bindParam(":privileges", $privileges);

            $stmt->execute();
            $connector->commit();

            return True;
        } catch (Exception $e) {
            if ($connector->inTransaction())
                $connector->rollBack();
            Application::APP()->error_logger->log(0, $e, __FILE__, __LINE__);
            return False;
        }
    }

    public function down()
    {
        $connector = Application::APP()->database->connector();
        $connector->exec("DELETE FROM `trains_system`.`users` WHERE username = 'admin'");
    }
}