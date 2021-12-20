<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/UserTable.php";

$ans = array();

//Phase 7: Add to Purchase
$mainDatabase = new BookShopMainDatabase();
$userTable = new UserTable($mainDatabase);
$select = $userTable->selectByUserId(UserTable::$PURCHASES_COLUMN, $_GET["userId"]);
$selectRow = $select->fetch_assoc();
$purchases = json_decode($selectRow[UserTable::$PURCHASES_COLUMN]);
$purchases[] = $_GET["note"];
$userTable->updatePurchaseByUserId(json_encode($purchases), $_GET["userId"]);

header("Location: http://bookshopkhu.sion-project.ir/Front-end/Profile.html?payment_status=1");