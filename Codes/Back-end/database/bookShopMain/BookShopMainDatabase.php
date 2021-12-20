<?php

class BookShopMainDatabase
{
    public $connection;

    function __construct()
    {
        include "bookShopMainDatabaseAuthenticationInformation.php";
        $this->connection = new mysqli($BookShopMainDatabaseServerName, $BookShopMainDatabaseUsername
            , $BookShopMainDatabasePassword, $BookShopMainDatabaseName);
    }
}