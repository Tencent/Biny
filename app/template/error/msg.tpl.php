<?php
/* @var $this Response */
/* @var $PRM BinyArray */
?>

<?php if (!App::$base->request->isAjax()){?>
<?php include App::$view_root . "/base/common.tpl.php" ?>
<?php include App::$view_root . "/base/header.tpl.php" ?>

<div class="container">
<?php } ?>

<div class="messageImage">
    <img src="<?=App::$base->config->get('webRoot')?>/static/images/source/error.gif" />
</div>
<div class="messageInfo"><?=$PRM['msg']?></div>
<div class="messageUrl">
    现在您可以：
    <a href="javascript:window.history.go(-1);" class='mlink'>[后退]</a>
    <a href="/" class='mlink'>[返回首页]</a>
</div>

<?php if (!App::$base->request->isAjax()){?>
</div>
<?php include App::$view_root . "/base/footer.tpl.php" ?>
<?php } ?>