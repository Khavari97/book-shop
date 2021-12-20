<?php

class AccessTokenTable
{
    protected $connection;

    static $TABLE_NAME = "access_token";
    static $ID_COLUMN = "id";
    static $ACCESS_TOKEN_COLUMN = "token";
    static $TARGET_COLUMN = "target";
    static $CREATED_AT_COLUMN = "created_at";
    static $EXPIRE_AT_COLUMN = "expire_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByTarget($requestedColumns, $target)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$TARGET_COLUMN . "`=$target";
        return $this->connection->query($sql);
    }

    function selectByAccessToken($requestedColumns, $accessToken)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$ACCESS_TOKEN_COLUMN . "`=\"$accessToken\"";
        return $this->connection->query($sql);
    }

    function insert($token, $target, $expireAt)
    {
        $sql = "INSERT INTO `" . self::$TABLE_NAME . "`(`" . self::$ACCESS_TOKEN_COLUMN . "`, `" . self::$TARGET_COLUMN . "`, `" . self::$CREATED_AT_COLUMN . "`, `" . self::$EXPIRE_AT_COLUMN . "`) VALUES (\"$token\",$target,\"" . date("Y-m-d H:i:s") . "\",\"$expireAt\")";
        return $this->connection->query($sql);
    }

    function deleteById($id)
    {
        $sql = "DELETE FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$ID_COLUMN . "` = $id";
        return $this->connection->query($sql);
    }
}