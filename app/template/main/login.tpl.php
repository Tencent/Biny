<?php
/* @var $this TXResponse */
/* @var $PRM TXArray */
?>

<? include TXApp::$view_root . "/base/common.tpl.php" ?>
<? include TXApp::$view_root . "/base/header.tpl.php" ?>
<style type="text/css">
    body {
        font-family: sans-serif;

        background-color: #323B55;
        background-image: -webkit-linear-gradient(bottom, #323B55 0%, #424F71 100%);
        background-image: -moz-linear-gradient(bottom, #323B55 0%, #424F71 100%);
        background-image: -o-linear-gradient(bottom, #323B55 0%, #424F71 100%);
        background-image: -ms-linear-gradient(bottom, #323B55 0%, #424F71 100%);
        background-image: linear-gradient(bottom, #323B55 0%, #424F71 100%);
    }

    .login {
        width: 220px;
        height: 155px;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -110px;
        margin-top: -75px;
    }

    .login input[type="text"],.login input[type="password"] {
        width: 100%;
        height: 40px;
        positon: relative;
        margin-top: 7px;
        font-size: 14px;
        color: #444;
        outline: none;
        border: 1px solid rgba(0, 0, 0, .49);

        padding-left: 20px;

        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
        border-radius: 6px;

        background-color: #fff;
        background-image: -webkit-linear-gradient(bottom, #FFFFFF 0%, #F2F2F2 100%);
        background-image: -moz-linear-gradient(bottom, #FFFFFF 0%, #F2F2F2 100%);
        background-image: -o-linear-gradient(bottom, #FFFFFF 0%, #F2F2F2 100%);
        background-image: -ms-linear-gradient(bottom, #FFFFFF 0%, #F2F2F2 100%);
        background-image: linear-gradient(bottom, #FFFFFF 0%, #F2F2F2 100%);

        -webkit-box-shadow: inset 0px 2px 0px #d9d9d9;
        box-shadow: inset 0px 2px 0px #d9d9d9;

    }

    .login input[type="text"]:focus,.login input[type="password"]:focus {
        -webkit-box-shadow: inset 0px 2px 0px #a7a7a7;
        box-shadow: inset 0px 2px 0px #a7a7a7;
    }

    .login input:first-child {
        margin-top: 0px;
    }

    .login input[type="submit"] {
        width: 100%;
        height: 50px;
        margin-top: 7px;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        text-shadow: 0px -1px 0px #5b6ddc;
        outline: none;
        border: 1px solid rgba(0, 0, 0, .49);

        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
        border-radius: 6px;

        background-color: #5466da;
        background-image: -webkit-linear-gradient(bottom, #5466da 0%, #768ee4 100%);
        background-image: -moz-linear-gradient(bottom, #5466da 0%, #768ee4 100%);
        background-image: -o-linear-gradient(bottom, #5466da 0%, #768ee4 100%);
        background-image: -ms-linear-gradient(bottom, #5466da 0%, #768ee4 100%);
        background-image: linear-gradient(bottom, #5466da 0%, #768ee4 100%);

        cursor: pointer;

        -webkit-box-shadow: inset 0px 1px 0px #9ab1ec;
        box-shadow: inset 0px 1px 0px #9ab1ec;

    }

    .login input[type="submit"]:hover {
        background-color: #5f73e9;
        background-image: -webkit-linear-gradient(bottom, #5f73e9 0%, #859bef 100%);
        background-image: -moz-linear-gradient(bottom, #5f73e9 0%, #859bef 100%);
        background-image: -o-linear-gradient(bottom, #5f73e9 0%, #859bef 100%);
        background-image: -ms-linear-gradient(bottom, #5f73e9 0%, #859bef 100%);
        background-image: linear-gradient(bottom, #5f73e9 0%, #859bef 100%);

        -webkit-box-shadow: inset 0px 1px 0px #aab9f4;
        box-shadow: inset 0px 1px 0px #aab9f4;

        /*margin-top: 10px;*/
    }

    .login input[type="submit"]:active {
        background-color: #7588e1;
        background-image: -webkit-linear-gradient(bottom, #7588e1 0%, #7184df 100%);
        background-image: -moz-linear-gradient(bottom, #7588e1 0%, #7184df 100%);
        background-image: -o-linear-gradient(bottom, #7588e1 0%, #7184df 100%);
        background-image: -ms-linear-gradient(bottom, #7588e1 0%, #7184df 100%);
        background-image: linear-gradient(bottom, #7588e1 0%, #7184df 100%);

        -webkit-box-shadow: inset 0px 1px 0px #93a9e9;
        box-shadow: inset 0px 1px 0px #93a9e9;
    }

</style>

<form class="login" action="/login">
    <input type="text" name="username" placeholder="用户名">
    <input type="text" name="_csrf" hidden value="<?=$this->getCsrfToken()?>"/>
    <input type="submit" value="Log In">
</form>



<? include TXApp::$view_root . "/base/footer.tpl.php" ?>