<style type="text/css">
    div {text-align: center}
</style>
<div style="margin-top: 100px">
    <img src="<?=TXApp::$base->config->get('webRoot')?>/static/images/source/error.gif" />
</div>
<div><?=$PRM['msg']?></div>
<div>
    现在您可以：
    <a href="javascript:window.history.go(-1);">[后退]</a>
    <a href="/">[返回首页]</a>
</div>