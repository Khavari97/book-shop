<?php

class ImageTable
{
    protected $connection;

    static $TABLE_NAME = "image";
    static $IMAGE_ID_COLUMN = "image_id";
    static $LINK_COLUMN = "link";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIds($requestedColumns, $encodedIds)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$IMAGE_ID_COLUMN . "`IN ($encodedIds);";
        return $this->connection->query($sql);
    }
}