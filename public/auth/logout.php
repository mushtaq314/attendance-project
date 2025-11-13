<?php
session_start();
session_destroy();
header('Location: /attendance-project/public/');
exit;
