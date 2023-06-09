<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$store_name = 'انتشارات کتاب پرگار';
$page_title = $this->session->userdata('page_title');
$page_meta_tag_title = $this->session->userdata('page_meta_tag_title');
$page_meta_tag_description = $this->session->userdata('page_meta_tag_description');
$page_meta_tag_keywords = $this->session->userdata('page_meta_tag_keywords');

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=isset($page_title) ? "$store_name | $page_title" : $store_name;?></title>
    <meta name="title" content="<?=isset($page_meta_tag_title) ? $page_meta_tag_title : $store_name;?>">
    <meta name="description" content="<?=isset($page_meta_tag_description) ? $page_meta_tag_description : $store_name;?>">
    <meta name="keywords" content="<?=isset($page_meta_tag_keywords) ? $page_meta_tag_keywords : $store_name;?>">

    <link rel="shortcut icon" href="<?PHP echo base_url('assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon image_src" href="<?PHP echo base_url('assets/images/logo_orang.png'); ?>">
    <link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/w3.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/swiper.min.css"); ?>">
    <!--<link rel="stylesheet" type="text/css" href="<?PHP /*echo base_url("assets/css/fontawesome/fontawesome-all.min.css"); */?>">-->
	<link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/fontawesome/all.min.css"); ?>">
    <link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/my_style.css"); ?>">

    <script>
        base_url = "<?=base_url()?>";
        currency_name = "تومان";
    </script>
</head>

<body>
