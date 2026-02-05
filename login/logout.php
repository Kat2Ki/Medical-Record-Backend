<?php
// logout.php
session_start();
$_SESSION = [];
session_unset();
session_destroy();
header('Location: cep.html');
exit();
?>
