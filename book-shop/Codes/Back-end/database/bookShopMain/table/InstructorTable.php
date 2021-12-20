<?php

class InstructorTable
{
    protected $connection;

    static $TABLE_NAME = "instructor";
    static $INSTRUCTOR_ID_COLUMN = "instructor_id";
    static $NAME_COLUMN = "name";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIds($requestedColumns, $encodedIds)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$INSTRUCTOR_ID_COLUMN . "`IN ($encodedIds);";
        return $this->connection->query($sql);
    }
}