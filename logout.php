<?php
session_start();
session_unset(); // Ella session variables-aiyum remove pannum
session_destroy(); // Session-ah morthama azhichidum

header("Location: lo.php");
exit();
?>