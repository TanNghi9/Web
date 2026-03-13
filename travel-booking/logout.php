<?php
require_once 'includes/db-connect.php';
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;
