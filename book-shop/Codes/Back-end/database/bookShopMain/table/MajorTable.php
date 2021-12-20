<?php

class MajorTable
{
    protected $connection;

    static $TABLE_NAME = "major";
    static $MAJOR_ID_COLUMN = "major_id";
    static $TITLE_COLUMN = "title";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIds($requestedColumns, $encodedIds)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$MAJOR_ID_COLUMN . "`IN ($encodedIds);";
        return $this->connection->query($sql);
    }
}