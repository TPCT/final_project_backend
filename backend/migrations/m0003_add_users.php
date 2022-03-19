<?php

namespace migrations;

use core\Application;
use Exception;

class m0003_add_users
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
            $username = "train1";
            $password = password_hash("train1234", PASSWORD_DEFAULT);
            $mac_address = "FF:FF:FF:FF:FF:F1";
            $privileges = 0;

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":mac_address", $mac_address);
            $stmt->bindParam(":privileges", $privileges);

            $stmt->execute();

            $stmt = $connector->prepare($query);
            $username = "train2";
            $password = password_hash("train2234", PASSWORD_DEFAULT);
            $mac_address = "FF:FF:FF:FF:FF:F2";
            $privileges = 0;

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":mac_address", $mac_address);
            $stmt->bindParam(":privileges", $privileges);

            $stmt->execute();

            $stmt = $connector->prepare($query);
            $username = "train3";
            $password = password_hash("train3234", PASSWORD_DEFAULT);
            $mac_address = "FF:FF:FF:FF:FF:F3";
            $privileges = 0;

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