<?php

class UserTable
{
    protected $connection;

    static $TABLE_NAME = "user";
    static $USER_ID_COLUMN = "user_id";
    static $EMAIL_COLUMN = "email";
    static $NAME_COLUMN = "name";
    static $PASSWORD_COLUMN = "password";
    static $MAJOR_COLUMN = "major";
    static $PAYMENT_COLUMN = "payment";
    static $PURCHASES_COLUMN = "purchases";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByEmailAndPassword($requestedColumns, $email, $password)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$EMAIL_COLUMN . "`=\"$email\" AND `" . self::$PASSWORD_COLUMN . "`=\"$password\"";
        return $this->connection->query($sql);
    }

    function selectByUserId($requestedColumns, $userId)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$USER_ID_COLUMN . "`=$userId";
        return $this->connection->query($sql);
    }

    function selectByEmail($requestedColumns, $email)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$EMAIL_COLUMN . "`=\"$email\"";
        return $this->connection->query($sql);
    }

    function updatePasswordByUserId($userId, $password)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$PASSWORD_COLUMN . "` = \"$password\" WHERE `" . self::$USER_ID_COLUMN . "` =$userId";
        return $this->connection->query($sql);
    }

    function updatePurchaseByUserId($purchase, $userId)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$PURCHASES_COLUMN . "` = '$purchase' WHERE `" . self::$USER_ID_COLUMN . "` =$userId";
        return $this->connection->query($sql);
    }
}