<?php

class BookTable
{
    protected $connection;

    static $TABLE_NAME = "book";
    static $BOOK_ID_COLUMN = "book_id";
    static $TITLE_COLUMN = "title";
    static $DESCRIPTION_COLUMN = "description";
    static $PRICE_COLUMN = "price";
    static $OWNER_COLUMN = "owner";
    static $WRITER_COLUMN = "writer";
    static $FILES_COLUMN = "files";
    static $IMAGES_COLUMN = "images";
    static $TAGS_COLUMN = "tags";
    static $CREATED_AT_COLUMN = "created_at";
    static $UPDATED_AT_COLUMN = "updated_at";

    function __construct($BookShopMainDatabase)
    {
        $this->connection = $BookShopMainDatabase->connection;
    }

    function selectByIdAndOwner($requestedColumns, $bookId, $owner)
    {
        $sql = "SELECT $requestedColumns FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$BOOK_ID_COLUMN . "`=$bookId AND `" . self::$OWNER_COLUMN . "`=$owner;";
        return $this->connection->query($sql);
    }

    function insert($title, $description, $price, $owner, $writer, $files, $images, $tags, $createdAt, $updatedAt)
    {
        $sql = "INSERT INTO `" . self::$TABLE_NAME . "`(`" . self::$TITLE_COLUMN . "`, `" . self::$DESCRIPTION_COLUMN . "`, `" . self::$PRICE_COLUMN . "`, `" . self::$OWNER_COLUMN . "`, `" . self::$WRITER_COLUMN . "`, `" . self::$FILES_COLUMN . "`, `" . self::$IMAGES_COLUMN . "`, `" . self::$TAGS_COLUMN . "`, `" . self::$CREATED_AT_COLUMN . "`, `" . self::$UPDATED_AT_COLUMN . "`) VALUES ('$title', '$description', '$price', '$owner', '$writer', '$files', '$images', '$tags', '$createdAt', '$updatedAt');";
        return $this->connection->query($sql);
    }

    function updateById($bookId, $title, $description, $price, $writer, $files, $images, $tags)
    {
        $encodedSet = "";
        if ($title !== NULL) {
            $encodedSet .= "`" . self::$TITLE_COLUMN . "`" . "='$title',";
        }
        if ($description !== NULL) {
            $encodedSet .= "`" . self::$DESCRIPTION_COLUMN . "`" . "='$description',";
        }
        if ($price !== NULL) {
            $encodedSet .= "`" . self::$PRICE_COLUMN . "`" . "='$price',";
        }
        if ($writer !== NULL) {
            $encodedSet .= "`" . self::$WRITER_COLUMN . "`" . "='$writer',";
        }
        if ($files !== NULL) {
            $encodedSet .= "`" . self::$FILES_COLUMN . "`" . "='" . json_encode($files) . "',";
        }
        if ($images !== NULL) {
            $encodedSet .= "`" . self::$IMAGES_COLUMN . "`" . "='" . json_encode($images) . "',";
        }
        if ($tags !== NULL) {
            $encodedSet .= "`" . self::$TAGS_COLUMN . "`" . "='" . json_encode($tags) . "',";
        }
        if ($encodedSet === "") return FALSE;
        $encodedSet .= "`" . self::$UPDATED_AT_COLUMN . "`='" . date("Y-m-d H:i:s") . "'";
        $sql = "UPDATE `" . self::$TABLE_NAME . "` SET $encodedSet WHERE `" . self::$BOOK_ID_COLUMN . "`=$bookId;";
        return $this->connection->query($sql);
    }

    function deleteById($bookId)
    {
        $sql = "DELETE FROM `" . self::$TABLE_NAME . "` WHERE `" . self::$BOOK_ID_COLUMN . "`=$bookId;";
        return $this->connection->query($sql);
    }
}