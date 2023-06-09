<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$store_name = 'انتشارات کتاب پرگار';
$page_title = $this->session->userdata('page_title');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <title><?=isset($page_title) ? "$store_name | $page_title" : $store_name;?></title>
    <link rel="shortcut icon" href="<?PHP echo base_url('assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon image_src" href="<?PHP echo base_url('assets/images/favicon.png'); ?>">
    <link rel="stylesheet" "<?PHP echo base_url("assets/css/w3.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/bootstrap.min.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/mystyle.css"); ?>">
    <link href="<?PHP echo base_url("assets/css/fontawesome/all.min.css"); ?>" rel="stylesheet">
    <link href="<?PHP echo base_url("assets/css/bootstrap-4-navbar.css"); ?>" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script>
        base_url = "<?=base_url()?>";
    </script>
</head>

<body class="<?PHP echo($page_name . "_page") ?>">
