<?php
/* @var $this TXResponse */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8;"/>
    <meta name="renderer" content="webkit">
    <META http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,user-scalable=yes">
    <meta name="keywords" content="<?=$this->encode($this->keywords) ?: "PHP框架"?>">
    <meta name="description" content="<?=$this->encode($this->descript) ?: "一款轻量级的PHP框架，兼容各种模式的web架构。"?>">

    <title><?=$this->encode($this->title) ?: "Biny"?></title>
    <link rel="icon" href="<?=$CDN_ROOT?>static/images/icon/favicon.ico" type="image/x-icon" />

    <link href="<?=$CDN_ROOT?>static/css/bootstrap.css" rel="stylesheet">
    <link href="<?=$CDN_ROOT?>static/css/main.css" rel="stylesheet" type="text/css"/>

</head>