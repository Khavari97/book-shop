<?php

class ConversationTable
{
    protected $connection;

    static $TABLE_NAME = "conversation";
    static $CONVERSATION_ID_COLUMN = "conversion_id";
    static $PARTICIPANT1_COLUMN = "participant1";
    static $PARTICIPANT2_COLUMN = "participant2";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }
}