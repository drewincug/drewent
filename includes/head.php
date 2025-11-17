<?php
    $tag="";
    if (isset($_GET['tag'])) {
        $tag=$_GET['tag'];
    }

    $tab="";
    if (isset($_GET['tab'])) {
        $tag=$_GET['tab'];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drew Enterprises – Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

   

  <style>
    body {
      background-color: #f7f9fc;
    }
    .navbar {
      background-color: #003366;
    }
    .nav-tabs .nav-link.active {
      background-color: #003366;
      color: #fff !important;
    }
    .nav-tabs .nav-link {
      color: #003366;
      font-weight: 600;
    }
  </style>
</head>
<body>