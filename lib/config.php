<?php
// 設定 資料庫 參數
if(!defined('DB_HOST')) define('DB_HOST', 'localhost');
if(!defined('DB_NAME')) define('DB_NAME', 'Procurement-Sales-Stock');
if(!defined('DB_USERNAME')) define('DB_USERNAME', 'PSS');
if(!defined('DB_PASSWORD')) define('DB_PASSWORD', 'YqFtsxnBIFdhGOTE');


// 其他參數
if(!defined('INVENTORY_CHECK_WEEK')) define('INVENTORY_CHECK_WEEK', 4); // 清點庫存間格時間(周)
if(!defined('EXECUTIVES_POSITION_ID')) define('EXECUTIVES_POSITION_ID', 1); // 業務主管的職務id
?>