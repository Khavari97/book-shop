<?php

class FileTable
{
    protected $connection;

    static $TABLE_NAME = "file";
    static $FILE_ID_COLUMN = "file_id";
    static $LINK_COLUMN = "link";
    static $SIZE_COLUMN = "size";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIds($requestedColumns, $encodedIds)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$FILE_ID_COLUMN . "`IN ($encodedIds);";
        return $this->connection->query($sql);
    }
}