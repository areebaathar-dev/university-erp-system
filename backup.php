<?php
session_start();
if($_SESSION['role'] != 'admin') die("Access Denied");

if(isset($_POST['backup'])){
    $file = 'backup_'.date('Ymd_His').'.sql';
    exec("mysqldump -u root university_erp > backups/$file");
    $conn->query("INSERT INTO backup_log(file_name) VALUES('$file')");
    $msg = "Backup created: $file";
}
?>