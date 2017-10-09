<?php
/* @var $this TXResponse */
/* @var $PRM TXArray */
?>

<?if (!TXApp::$base->request->isAjax()){?>
<? include TXApp::$view_root . "/base/common.tpl.php" ?>
<? include TXApp::$view_root . "/base/header.tpl.php" ?>

<div class="container">
<?}?>

<div class="messageImage">
    <img src="<?=TXConfig::getConfig('webRoot')?>/static/images/source/error.gif" />
</div>
<div class="messageInfo"><?=$PRM['msg']?></div>
<div class="messageUrl">
    现在您可以：
    <a href="javascript:window.history.go(-1);" class='mlink'>[后退]</a>
    <a href="/" class='mlink'>[返回首页]</a>
</div>

<?if (!TXApp::$base->request->isAjax()){?>
</div>
<? include TXApp::$view_root . "/base/footer.tpl.php" ?>
<?}?>