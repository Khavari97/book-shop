<?php

class MessageTable
{
    protected $connection;
    protected $conversationId;

    static $TABLE_PREFIX = "message_";
    static $MESSAGE_ID_COLUMN = "message_id";
    static $CONTENT_COLUMN = "content";
    static $DIRECTION_COLUMN = "direction";
    static $SEEN_COLUMN = "seen";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase, $conversationId)
    {
        $this->connection = $BookShopMainDatabase->connection;
        $this->conversationId = $conversationId;
    }
}