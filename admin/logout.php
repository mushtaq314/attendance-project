<?php
session_start();
session_destroy();
header("Location: /attendance-project/admin/login.php");
exit();
?>
