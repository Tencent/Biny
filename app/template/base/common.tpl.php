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
    <meta name="keywords" content="<?=$this->encode($this->keywords) ?: "Biny, PHP, framework"?>">
    <meta name="description" content="<?=$this->encode($this->descript) ?: "Biny is a tiny, high-performance PHP framework for web applications "?>">

    <title><?=$this->encode($this->title) ?: "Biny"?></title>
    <link rel="icon" href="<?=$webRoot?>/static/images/icon/favicon.ico" type="image/x-icon" />

    <link href="<?=$webRoot?>/static/css/bootstrap.css" rel="stylesheet">
    <link href="<?=$webRoot?>/static/css/main.css" rel="stylesheet" type="text/css"/>

</head>