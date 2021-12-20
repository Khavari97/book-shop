<?php

class CourseTable
{
    protected $connection;

    static $TABLE_NAME = "course";
    static $COURSE_ID_COLUMN = "course_id";
    static $TITLE_COLUMN = "title";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIds($requestedColumns, $encodedIds)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$COURSE_ID_COLUMN . "`IN ($encodedIds);";
        return $this->connection->query($sql);
    }
}