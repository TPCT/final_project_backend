<?php

namespace models\embedded;

use Exception;
use helpers\LogEventHelper;
use PDO;

class ApiLogEventModel extends \core\Model
{
    public string $event_type = "";
    public mixed $event_data = "";
    private string $username;
    private string $mac_address;
    private string $train_id;

    public function __construct(){
        $this->username = $_SESSION['user_info']['username'];
        $this->mac_address = $_SESSION['user_info']['mac_address'];
        $this->train_id = $_SESSION['user_info']['user_id'];
    }
    public function Rules(){
        return [
            'event_type' => [self::RULE_REQUIRED],
            'event_data' => [self::RULE_REQUIRED]
        ];
    }

    private function setAccessTime(): void
    {
        $current_time = strftime("%F %X");
        $query = "UPDATE `trains_system`.`users` SET last_access_time=:current_time WHERE id=:id";
        $stmt = $this->prepare($query);
        $stmt->bindParam(':current_time', $current_time);
        $stmt->bindParam(":id", $this->train_id);
        $stmt->execute();
    }

    private function logNormalEvent(): void
    {
        $event = LogEventHelper::NormalEvent($this->username, $this->mac_address, $this->event_data);
        $query = "INSERT INTO `trains_system`.`train_event`(train_id, event) VALUES (:train_id, :event)";
        $stmt = $this->prepare($query);
        $stmt->bindParam(":train_id", $this->train_id, PDO::PARAM_INT);
        $stmt->bindParam(":event", $event);
        $stmt->execute();
    }

    private function logEmergencyEvent(): void
    {
        $event = LogEventHelper::EmergencyEvent($this->username, $this->mac_address, $this->event_data);
        $query = "INSERT INTO `trains_system`.`train_event`(train_id, event, emergent) VALUES (:train_id, :event, 1)";
        $stmt = $this->prepare($query);
        $stmt->bindParam(":train_id", $this->train_id, PDO::PARAM_INT);
        $stmt->bindParam(":event", $event);
        $stmt->execute();
    }
    public function logEvent(): bool{
        try{
            $this->beginTransaction();
            $this->setAccessTime();
            $this->logNormalEvent();
            if ($this->event_type === "emergency")
                $this->logEmergencyEvent();
            return $this->commit();
        }catch(Exception $e){
            if ($this->connection()->inTransaction())
                $this->rollback();
            throw $e;
        }
    }
}