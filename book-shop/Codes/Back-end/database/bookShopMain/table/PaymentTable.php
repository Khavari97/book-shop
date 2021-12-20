<?php

class PaymentTable
{
    protected $connection;

    static $TABLE_NAME = "payment";
    static $PAYMENT_ID_COLUMN = "payment_id";
    static $NOTE_COLUMN = "note";
    static $TARGET_COLUMN = "target";
    static $CREATED_AT_COLUMN = "created_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function insert($note, $target)
    {
        $sql = "INSERT INTO " . self::$TABLE_NAME . " (`" . self::$NOTE_COLUMN . "`, `" . self::$TARGET_COLUMN . "`, `" . self::$CREATED_AT_COLUMN . "`) VALUES($note,$target,'" . date("Y-m-d H:i:s") . "')";
        return $this->connection->query($sql);
    }
}