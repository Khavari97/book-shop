<?php

class EmailVerificationTable
{
    protected $connection;

    static $TABLE_NAME = "email_verification";
    static $EMAIL_COLUMN = "email";
    static $VERIFICATION_CODE_COLUMN = "verification_code";
    static $EMAIL_VERIFIED_COLUMN = "email_verified";
    static $CREATED_AT_COLUMN = "created_at";
    static $EXPIRE_AT_COLUMN = "expire_at";
    static $ATTEMPTS_NUMBER_COLUMN = "attempts_number";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByEmail($requestedColumns, $email)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$EMAIL_COLUMN . "`=\"$email\"";
        return $this->connection->query($sql);
    }

    function updateByEmail($email, $verificationCode, $emailVerified, $expireAt, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$VERIFICATION_CODE_COLUMN . "`=\"$verificationCode\", `" . self::$EMAIL_VERIFIED_COLUMN . "`=" . $emailVerified . ", `" . self::$CREATED_AT_COLUMN . "`=\"" . date("Y-m-d H:i:s") . "\", `" . self::$EXPIRE_AT_COLUMN . "`= \"$expireAt\", `" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$EMAIL_COLUMN . "` = \"$email\"";
        return $this->connection->query($sql);
    }

    function updateVerificationCodeAndAttemptsNumberByEmail($email, $verificationCode, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$VERIFICATION_CODE_COLUMN . "`=\"$verificationCode\",`" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$EMAIL_COLUMN . "` = \"$email\"";
        return $this->connection->query($sql);
    }

    function updateAttemptsNumberByEmail($email, $attemptsNumber)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$ATTEMPTS_NUMBER_COLUMN . "` = $attemptsNumber WHERE `" . self::$EMAIL_COLUMN . "` = \"$email\"";
        return $this->connection->query($sql);
    }

    function updateEmailVerifiedByEmail($email, $emailVerified)
    {
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET `" . self::$EMAIL_VERIFIED_COLUMN . "` = $emailVerified WHERE `" . self::$EMAIL_COLUMN . "` = \"$email\"";
        return $this->connection->query($sql);
    }

    function insert($email, $verificationCode, $emailVerified, $expireAt, $attemptsNumber)
    {
        $sql = "INSERT INTO `" . self::$TABLE_NAME . "`(`" . self::$EMAIL_COLUMN . "`, `" . self::$VERIFICATION_CODE_COLUMN . "`, `" . self::$EMAIL_VERIFIED_COLUMN . "`, `" . self::$CREATED_AT_COLUMN. "`, `" . self::$EXPIRE_AT_COLUMN. "`, `" . self::$ATTEMPTS_NUMBER_COLUMN . "`) VALUES (\"$email\",\"$verificationCode\",$emailVerified,\"" . date("Y-m-d H:i:s") . "\",\"$expireAt\",$attemptsNumber)";
        return $this->connection->query($sql);
    }
}