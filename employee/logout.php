<?php
session_start();
session_destroy();
header("Location: /attendance-project/employee/login.php");
exit();
?>
