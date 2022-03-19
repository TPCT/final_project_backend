<?php

namespace models\embedded;

use Exception;
use helpers\LogEventHelper;
use helpers\SessionHelper;
use PDO;

class ApiAuthenticationModel extends \core\Model
{
    public string $username = "";
    public string $password = "";
    public string $mac_address = "";

    public function rules(): array
    {
        return [
            'username' => [
                self::RULE_REQUIRED
            ],
            'password' => [
                self::RULE_REQUIRED
            ],
            'mac_address' => [
                self::RULE_REQUIRED,
                [self::RULE_VALIDATE, 'validator' => function(){
                    if (strlen($this->mac_address) !== strlen("FF:FF:FF:FF"))
                        return False;
                    $pattern = '/[0-9a-f]{2}\:[0-9a-f]{2}\:[0-9a-f]{2}\:[0-9a-f]{2}/i';
                    return (bool)preg_match($pattern, $this->mac_address);
                }]
            ]
        ];
    }

    private function trainExists(){
        $query = "SELECT * FROM `trains_system`.`users` WHERE username=:username AND mac_address=:mac_address";
        $stmt = $this->prepare($query);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":mac_address", $this->mac_address);

        if (!$stmt->execute() || !($train_info = $stmt->fetch()))
            return False;

        if (password_verify($this->password, $train_info['password']))
            return $train_info['id'];

        return False;
    }

    private function setAccessTime($train_id): void
    {
        $current_time = strftime("%F %X");
        $query = "UPDATE `trains_system`.`users` SET last_access_time=:current_time WHERE id=:id";
        $stmt = $this->prepare($query);
        $stmt->bindParam(':current_time', $current_time);
        $stmt->bindParam(":id", $train_id);
        $stmt->execute();
    }

    private function logLoginEvent($train_id): void
    {
        $event = LogEventHelper::loginEvent($this->username, $this->mac_address);
        $query = "INSERT INTO `trains_system`.`train_event`(train_id, event) VALUES (:train_id, :event)";
        $stmt = $this->prepare($query);
        $stmt->bindParam(":train_id", $train_id, PDO::PARAM_INT);
        $stmt->bindParam(":event", $event);
        $stmt->execute();
    }

    public function login(): bool{
        try{
            if (($train_id = $this->trainExists()) === False)
                return False;

            $this->beginTransaction();
            $this->setAccessTime($train_id);
            $this->logLoginEvent($train_id);

            if ($this->commit())
                return SessionHelper::setSessionInfo($this->username, $this->mac_address, 0, $train_id);


            if ($this->connection()->inTransaction())
                $this->rollBack();

            return False;

        }catch(Exception $e){
            if ($this->connection()->inTransaction())
                $this->rollback();
            throw $e;
        }
    }
}