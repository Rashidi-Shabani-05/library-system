<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="adminlte/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="adminlte/plugins/bootstrap-icons/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <?php require __DIR__ . "/navbar.php"; ?>
    <?php require __DIR__ . "/sidebar.php"; ?>
    <main class="app-main">
        <div class="app-content">
            <div class="container-fluid pt-3">