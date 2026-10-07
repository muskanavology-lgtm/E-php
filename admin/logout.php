<?php
require_once __DIR__ . '/../classes/Admin.php';
Admin::logout();
header('Location: login.php?loggedout=1');
exit;
