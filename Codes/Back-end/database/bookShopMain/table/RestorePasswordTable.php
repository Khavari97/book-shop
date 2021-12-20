<?php

class RestorePasswordTable
{
    protected $connection;

    static $TABLE_NAME = "restore_password";
    static $TARGET_COLUMN = "target";
    static $RESTORE_CODE_COLUMN = "restore_code";
    static $CREATED_AT_COLUMN = "created_at";
    static $EXPIRE_AT_COLUMN = "expire_at";
    static $ATTEMPTS_NUMBER_COLUMN = "attempts_number";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByTarget($requestedColumns, $target)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$TARGET_COLUMN . "`=$target";
        return $this->connection->query($sql);
    }

    function updateByTarget($target, $restoreCode, $expireAt, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$RESTORE_CODE_COLUMN . "`=\"$restoreCode\", `" . self::$CREATED_AT_COLUMN . "`=\"" . date("Y-m-d H:i:s") . "\", `" . self::$EXPIRE_AT_COLUMN . "`= \"$expireAt\", `" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$TARGET_COLUMN . "` =$target";
        return $this->connection->query($sql);
    }

    function updateRestoreCodeAndAttemptsNumberByTarget($target, $restoreCode, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$RESTORE_CODE_COLUMN . "`=\"$restoreCode\", `" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$TARGET_COLUMN . "` =$target";
        return $this->connection->query($sql);
    }

    function updateAttemptsNumberByTarget($target, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$TARGET_COLUMN . "` =$target";
        return $this->connection->query($sql);
    }

    function insert($target, $restoreCode, $expireAt, $attemptsNumber)
    {
        $sql = "INSERT INTO `" . self::$TABLE_NAME . "`(`" . self::$TARGET_COLUMN . "`, `" . self::$RESTORE_CODE_COLUMN . "`, `" . self::$CREATED_AT_COLUMN . "`, `" . self::$EXPIRE_AT_COLUMN . "`, `" . self::$ATTEMPTS_NUMBER_COLUMN . "`) VALUES ($target,\"$restoreCode\",\"" . date("Y-m-d H:i:s") . "\",\"$expireAt\",$attemptsNumber)";
        return $this->connection->query($sql);
    }

    function deleteByTarget($target)
    {
        $sql = "DELETE FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$TARGET_COLUMN . "`= $target";
        return $this->connection->query($sql);
    }
}