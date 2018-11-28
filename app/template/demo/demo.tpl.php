<?php include TXApp::$view_root . "/base/common.tpl.php" ?>
<?php include TXApp::$view_root . "/base/header.tpl.php" ?>
<link href="<?=$webRoot?>/static/css/demo.css" rel="stylesheet" type="text/css"/>

<!-- Docs master nav -->
<header class="navbar navbar-static-top navbar-inverse" id="top" role="banner">
    <div class="container">
        <a href="<?=$webRoot?>/demo/" class="navbar-brand">Biny Framework Wiki</a>
        <div class="pull-right" style="margin-right: 15%">
            <a class="navbar-brand <?php if ($PRM['lan']==='cn'){?>active<?php } ?>" href="javascript:void(0)" onclick="changeLanguage('cn')">中文</a>
            <a class="navbar-brand <?php if ($PRM['lan']==='en'){?>active<?php } ?>" href="javascript:void(0)" onclick="changeLanguage('en')">English</a>
        </div>
    </div>
</header>

<div class="container bs-docs-container">

<div class="row">
<div <?php if (TXApp::$base->request->isMobile()){?>class="col-md-12"<?php } else {?> class="col-md-9" <?php } ?> role="main">
    <div class="bs-docs-section">
        <h1 id="overview" class="page-header">概览</h1>
        <p>Biny是一款高性能的轻量级PHP框架</p>
        <p>遵循 MVC 模式，用于快速开发现代 Web 应用程序</p>
        <p>Biny代码简洁优雅，对应用层，数据层，模板渲染层的封装简单易懂，能够快速上手使用</p>
        <p>高性能，框架响应时间在1ms以内，单机qps轻松上3000</p>

        <h2 id="overview-introduce">介绍</h2>
        <p>支持跨库连表，条件复合筛选，查询PK缓存等</p>
        <p>同步异步请求分离，类的自动化加载管理</p>
        <p>支持Form表单验证，支持事件触发机制</p>
        <p>支持浏览器端调试，快速定位程序问题和性能瓶颈</p>
        <p>具有sql防注入，html自动防xss等特性</p>
        <p>框架 Wiki：<a href="http://www.billge.cc">http://www.billge.cc</a></p>
        <p>GitHub 地址：<a href="https://github.com/Tencent/Biny">https://github.com/Tencent/Biny</a></p>

        <h2 id="overview-files">目录结构</h2>
        <div class="col-lg-3"><img src="http://f.wetest.qq.com/gqop/10000/20000/GuideImage_cb2a0980064cb1e61242742ed0b183be.png"></div>
        <div class="col-lg-8" style="margin-left: 20px">
            <p><code>/app/</code> 总工作目录</p>
            <p><code>/app/config/</code> 业务配置层</p>
            <p><code>/app/controller/</code> 路由入口Action层</p>
            <p><code>/app/dao/</code> 数据库表实例层</p>
            <p><code>/app/event/</code> 事件触发及定义层</p>
            <p><code>/app/form/</code> 表单定义及验证层</p>
            <p><code>/app/model/</code> 自定义模型层</p>
            <p><code>/app/service/</code> 业务逻辑层</p>
            <p><code>/app/template/</code> 页面渲染层</p>
            <p><code>/config/</code> 框架配置层</p>
            <p><code>/lib/</code> 系统Lib层</p>
            <p><code>/extends/</code> 自定义Lib层（替代原vendor目录，该目录下内容用户都可以根据需要自行替换删除）</p>
            <p><code>/logs/</code> 工作日志目录</p>
            <p><code>/web/</code> 总执行入口</p>
            <p><code>/web/static/</code> 静态资源文件</p>
            <p><code>/web/index.php</code> 总执行文件</p>
            <p><code>/shell.php</code> shell模式执行入口</p>
        </div>
        <div style="clear: both"></div>

        <h2 id="overview-level">调用关系</h2>
        <p><code>Action</code>为总路由入口，<code>Action</code>可调用私有对象<code>Service</code>业务层 和 <code>DAO</code>数据库层</p>
        <p><code>Service</code>业务层 可调用私有对象<code>DAO</code>数据库层</p>
        <p>程序全局可调用lib库下系统方法，例如：<code>TXLogger</code>（调试组件）</p>
        <p><code>TXApp::$base</code>为全局单例类，可全局调用</p>
        <p><code>TXApp::$base->request</code> 为当前请求，可获取当前地址，客户端ip等</p>
        <p><code>TXApp::$base->session</code> 为系统session，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->memcache</code> 为系统memcache，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->redis</code> 为系统redis，可直接获取和复制，设置过期时间</p>

        <p>用户可以在<code>/app/model/</code>下自定义model数据类，通过<code>TXApp::$model</code>获取，例如：</p>
        <p><code>TXApp::$model->person</code> 为当前用户，可在<code>/app/model/person.php</code>中定义</p>

        <p>简单示例</p>
        <pre class="code"><sys>namespace</sys> app\controller;
<sys>use</sys> TXApp;
<span class="nc">/**
* 主页Action
* @property \app\service\projectService $projectService
* @property \app\dao\projectDAO $projectDAO
*/  </span>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>// init方法会在action执行前被执行</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>// 未登录时调整登录页面</note>
        <sys>if</sys>(!TXApp::<prm>$model</prm>-><prm>person</prm>-><func>exist</func>()){
            <sys>return</sys> TXApp::<prm>$base</prm>-><prm>request</prm>-><func>redirect</func>(<str>'/auth/login/'</str>);
        }
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 获取当前用户</note>
        <prm>$person</prm> = TXApp::<prm>$model</prm>-><prm>person</prm>;
        <prm>$members</prm> = TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>get</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>);
        <sys>if</sys> (!<prm>$members</prm>){
            <note>// 获取用户所在项目成员</note>
            <prm>$project</prm> = <prm>$this</prm>-><prm>projectDAO</prm>-><func>find</func>(<sys>array</sys>(<str>'id'</str>=><prm>$person</prm>-><prm>project_id</prm>));
            <prm>$members</prm> = <prm>$this</prm>-><prm>projectService</prm>-><func>getMembers</func>(<prm>$project</prm>[<str>'id'</str>]);
            TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>set</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>, <prm>$members</prm>);
        }
        <note>//返回 project/members.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'project/members'</str>, <sys>array</sys>(<str>'members'</str>=><prm>$members</prm>));
    }
}</pre>
        <p>P.S: 示例中的用法会在下面具体展开介绍</p>

        <h2 id="overview-index">环境配置</h2>
        <p>PHP版本必须在<code>5.5</code>以上，包含<code>5.5</code></p>
        <p>如果需要用到数据库，则需要安装并启用<code>mysqli扩展</code></p>
        <p><code>php.ini</code>配置中则需要把<code>short_open_tag</code>打开</p>
        <p><code>/config/autoload.php</code> 为自动加载配置类，必须具有<code>写权限</code></p>
        <p><code>/logs/</code> 目录为日志记录文件夹，也必须具有<code>写权限</code></p>
        <p>本例子中主要介绍linux下nginx的配置</p>
        <p>nginx根目录需要指向<code>/web/</code>目录下，示例如下</p>
        <pre class="code"><sys>location</sys> / {
    <const>root</const>   /data/billge/biny/web/; <note>// 这里为框架/web目录的绝对路径</note>
    <act>index</act>  index.php index.html index.htm;
    <act>try_files</act> $uri $uri/ /index.php?$args;
}</pre>
        <p>Apache 配置如下：</p>
<pre class="code"><note># 设置文档根目录为框架/web目录</note>
<const>DocumentRoot</const> <str>"/data/billge/biny/web/"</str>

&lt;<const>Directory</const> <str>"/data/billge/biny/web/"</str>>
    <act>RewriteEngine</act> <sys>on</sys>
    <note># 如果请求的是真实存在的文件或目录，直接访问</note>
    <act>RewriteCond</act> %{REQUEST_FILENAME} !-f
    <act>RewriteCond</act> %{REQUEST_FILENAME} !-d
    <note># 如果请求的不是真实文件或目录，分发请求至 index.php</note>
    <act>RewriteRule</act> . index.php

    <note># 以下三行apache默认会有，如无法正常使用请自行添加</note>
    <note># Options +Indexes +Includes +FollowSymLinks +MultiViews</note>
    <note># AllowOverride All</note>
    <note># Require local</note>

    <note># ...other settings...  </note>
&lt;/<const>Directory</const>> </pre>
        <p><code>/web/index.php</code>是程序的主入口，其中有几个关键配置</p>
        <pre class="code"><note>//默认时区配置</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// 开启debug调试模式（会输出异常）</note>
<sys>defined</sys>(<str>'SYS_DEBUG'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_DEBUG'</str>, <sys>true</sys>);
<note>// 开启Logger页面调试</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);
<note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
<note>// 系统维护中。。。</note>
<sys>defined</sys>(<str>'isMaintenance'</str>) <sys>or</sys> <sys>define</sys>(<str>'isMaintenance'</str>, <sys>false</sys>);</pre>

        <p>其中<code>SYS_ENV</code>的环境值也有bool型，方便判断使用</p>
        <pre class="code"><note>// 在\lib\TXApp.php 中配置</note>
<note>// 测试环境</note>
<sys>defined</sys>(<str>'ENV_DEV'</str>) <sys>or define</sys>(<str>'ENV_DEV'</str>, <const>SYS_ENV</const> === 'dev');
<note>// 预发布环境</note>
<sys>defined</sys>(<str>'ENV_PRE'</str>) <sys>or define</sys>(<str>'ENV_PRE'</str>, <const>SYS_ENV</const> === 'pre');
<note>// 线上正式环境</note>
<sys>defined</sys>(<str>'ENV_PUB'</str>) <sys>or define</sys>(<str>'ENV_PUB'</str>, <const>SYS_ENV</const> === 'pub');</pre>
    </div>

    <div class="bs-docs-section">
        <h1 id="router">路由</h1>
        <p>基本MVC架构路由模式，第一层对应<code>action</code>，第二层对应<code>method</code>（默认<code>index</code>）</p>
        <h2 id="router-rule">默认路由</h2>
        <p>在<code>/app/controller</code>目录下，文件可以放在任意子目录或孙目录中。但必须确保文件名与类名一致，且不重复</p>
        <p>示例：/app/controller/Main/testAction.php</p>
        <pre class="code"><note>// http://www.billge.cc/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回 test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/test'</str>);
    }
}</pre>
        <p>同时也能在同一文件内配置多个子路由</p>
        <pre class="code"><note>//子路由查找action_{$router}</note>
<note>// http://www.billge.cc/test/demo1</note>
<sys>public function</sys> <act>action_demo1</act>()
{
    <note>//返回 test/demo1.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo1'</str>);
}

<note>// http://www.billge.cc/test/demo2</note>
<sys>public function</sys> <act>action_demo2</act>()
{
    <note>//返回 test/demo2.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo2'</str>);
}</pre>

        <h2 id="router-custom">自定义路由</h2>
        <p>除了上述默认路由方式外还可以自定义路由规则，可在<code>/config/config.php</code>中配置</p>
        <p>自定义路由规则会先被执行，匹配失败后走默认规则，参数冒号后面的字符串会自动转化为<code>正则匹配符</code></p>
<pre class="code"><note>/config/config.php</note>
<str>'routeRule'</str> => <sys>array</sys>(
    <note>// test/(\d+).html 的路由会自动转发到testAction中的 action_view方法</note>
    <str>'<prm>test</prm>/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/view'</str>,
    <note>// 匹配的参数可在转发路由中动态使用</note>
    <str>'<prm>test</prm>/&lt;<prm>method</prm>:[\w_]+&gt;/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/&lt;<prm>method</prm>&gt;'</str>,
),

<note>/app/controller/testAction.php</note>
<note>// test/272.html 正则匹配的内容会传入方法</note>
<sys>public function</sys> <act>action_view</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 272</note>
}

<note>// test/my_router/123.html</note>
<sys>public function</sys> <act>action_my_router</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 123</note>
}
</pre>


        <h2 id="router-ajax">异步请求</h2>
        <p>异步请求包含POST，ajax等多种请求方式，系统会自动进行<code>异步验证（csrf）</code>及处理</p>
        <p>程序中响应方法和同步请求保持一致，返回<code>$this->error()</code>会自动和同步请求作区分，返回<code>json数据</code></p>
        <pre class="code"><note>// http://www.billge.cc/test/demo3</note>
<sys>public function</sys> <act>action_demo3</act>()
{
    <prm>$ret</prm> = <sys>array</sys>(<str>'result'</str>=>1);
    <note>//返回 json {"flag": true, "ret": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>correct</func>(<prm>$ret</prm>);

    <note>//返回 json {"flag": false, "error": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<prm>$ret</prm>);
}</pre>
        <p>框架提供了一整套<code>csrf验证</code>机制，默认<code>开启</code>，可通过在Action中将<code>$csrfValidate = false</code>关闭。</p>
        <pre class="code"><note>// http://www.billge.cc/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//关闭csrf验证</note>
    <sys>protected</sys> <prm>$csrfValidate</prm> = <sys>false</sys>;

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回 test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    }
}</pre>

        <p>当csrf验证开启时，前端ajax请求需要预先加载引用<code>/static/js/main.js</code>文件，ajax提交时，系统会自动加上验证字段。</p>
        <p>POST请求同样也会触发csrf验证，需要在form中添加如下数据字段：</p>
        <pre class="code"><note>// 加在form中提交</note>
<act>&lt;input</act> type="<str>text</str>" name="<str>_csrf</str>" hidden value="<sys>&lt;?=</sys><prm>$this</prm>-><func>getCsrfToken</func>()<sys>?&gt;</sys>"<act>/></act></pre>

        <p>同样也可以在js中获取（前提是引用<code>/static/js/main.js</code>JS文件），加在POST参数中即可。</p>
        <pre class="code"><sys>var</sys> <prm>_csrf</prm> = <func>getCookie</func>(<str>'csrf-token'</str>);</pre>


        <h2 id="router-restful">Restful</h2>
        <p>Biny也同时支持restful协议的请求，可以在Action类中将<code>$restApi</code>置为<code>true</code>，则该Action会以restful的协议来解析路由</p>
        <pre class="code"><sys>namespace</sys> app\controller;
<note>/**
 * restful演示
 * @property \app\dao\userDAO $userDAO
 */</note>
<sys>class</sys> restAction <sys>extends</sys> baseAction
{
    <note>// 该action以restful协议解析路由</note>
    <sys>protected</sys> <prm>$restApi</prm> = <sys>true</sys>;

    <note>// [GET] http://www.billge.cc/rest/?id=xxx</note>
    <sys>public function</sys> <act>GET_index</act>(<prm>$id</prm>)
    {
        <prm>$user</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>([<str>'id'</str>=><prm>$id</prm>])-><func>find</func>();
        <sys>return</sys> <prm>$user</prm> ? <prm>$this</prm>-><func>correct</func>(<prm>$user</prm>) : <prm>$this</prm>-><func>error</func>(<str>'user not found'</str>);
    }

    <note>// [POST] http://www.billge.cc/rest/test</note>
    <sys>public function</sys> <act>POST_test</act>()
    {
        <prm>$user</prm> = <prm>$this</prm>-><func>param</func>(<str>'user'</str>);
        <prm>$user_id</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>add</func>(<prm>$user</prm>);
        <sys>return</sys> <prm>$user_id</prm> ? <prm>$this</prm>-><func>correct</func>(<prm>$user</prm>) : <prm>$this</prm>-><func>error</func>(<str>'data error'</str>);
    }

    <note>// [PUT] http://www.billge.cc/rest/?id=xxx</note>
    <sys>public function</sys> <act>PUT_index</act>(<prm>$id</prm>)
    {
        <prm>$user</prm> = <prm>$this</prm>-><func>param</func>(<str>'user'</str>);
        <prm>$ret</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>([<str>'id'</str>=><prm>$id</prm>])-><func>update</func>(<prm>$user</prm>);
        <sys>return</sys> <prm>$ret</prm> ? <prm>$this</prm>-><func>correct</func>() : <prm>$this</prm>-><func>error</func>(<str>'data error'</str>);
    }

    <note>// [PATCH] http://www.billge.cc/rest/test?id=xxx</note>
    <sys>public function</sys> <act>PATCH_test</act>(<prm>$id</prm>)
    {
        <prm>$sets</prm> = <prm>$this</prm>-><func>param</func>(<str>'sets'</str>);
        <prm>$ret</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>([<str>'id'</str>=><prm>$id</prm>])-><func>update</func>(<prm>$sets</prm>);
        <sys>return</sys> <prm>$ret</prm> ? <prm>$this</prm>-><func>correct</func>() : <prm>$this</prm>-><func>error</func>(<str>'data error'</str>);
    }

    <note>// [DELETE] http://www.billge.cc/rest/test?id=xxx</note>
    <sys>public function</sys> <act>DELETE_test</act>(<prm>$id</prm>)
    {
        <prm>$ret</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>([<str>'id'</str>=><prm>$id</prm>])-><func>delete</func>();
        <sys>return</sys> <prm>$ret</prm> ? <prm>$this</prm>-><func>correct</func>() : <prm>$this</prm>-><func>error</func>(<str>'data error'</str>);
    }
}</pre>

        <p>同样，restful协议也可以通过自定义路由的模式来配置，例如</p>
        <pre class="code"><note>/config/config.php</note>
<str>'routeRule'</str> => <sys>array</sys>(
    <note>// rest/(\d+) 的restful路由会自动转发到restAction中的 {method}_test方法</note>
    <str>'<prm>rest</prm>/&lt;<prm>id</prm>:\d+&gt;'</str> => <str>'rest/test'</str>,
    <note>// 匹配的参数可在转发路由中动态使用</note>
    <str>'<prm>v</prm>&lt;<prm>version</prm>:\d+&gt;/rest/&lt;<prm>id</prm>:\d+&gt;/&lt;<prm>method</prm>:[\w_]+&gt;'</str> => <str>'rest/&lt;<prm>method</prm>&gt;'</str>,
),

<note>/app/controller/restAction.php</note>
<note>// [DELETE] http://www.billge.cc/v2/rest/123/person</note>
<sys>public function</sys> <act>DELETE_person</act>(<prm>$version</prm>, <prm>$id</prm>)
{
    <sys>echo</sys> <prm>$version</prm>; <note>// 2</note>
    <sys>echo</sys> <prm>$id</prm>; <note>// 123</note>
}
<note>// [PUT] http://www.billge.cc/rest/272 正则匹配的内容会传入方法</note>
<sys>public function</sys> <act>PUT_test</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 272</note>
}
</pre>

        <h2 id="router-param">参数传递</h2>
        <p>方法可以直接接收 GET 参数，并可以赋默认值，空则返回null</p>
        <pre class="code"><note>// http://www.billge.cc/test/demo4/?id=33</note>
<sys>public function</sys> <act>action_demo4</act>(<prm>$id</prm>=10, <prm>$type</prm>, <prm>$name</prm>=<str>'biny'</str>)
{
    <note>// 33</note>
    <sys>echo</sys>(<prm>$id</prm>);
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$type</prm>);
    <note>// 'biny'</note>
    <sys>echo</sys>(<prm>$name</prm>);
}</pre>

        <p>同时也可以调用<code>param</code>，<code>get</code>，<code>post</code> 方法获取参数。</p>
        <p><code>param($key, $default)</code> 获取GET/POST/JSON参数{$key}, 默认值为{$default}</p>
        <p><code>get($key, $default)</code> 获取GET参数{$key}, 默认值为{$default}</p>
        <p><code>post($key, $default)</code> 获取POST参数{$key}, 默认值为{$default}</p>
        <p><code>getJson($key, $default)</code> 如果传递过来的参数为完整json流可使用该方法获取</p>
        <pre class="code"><note>// http://www.billge.cc/test/demo5/?id=33</note>
<sys>public function</sys> <act>action_demo5</act>()
{
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>param</func>(<str>'name'</str>));
    <note>// 'install'</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>post</func>(<str>'type'</str>, <str>'install'</str>));
    <note>// 33</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>get</func>(<str>'id'</str>, 1));
}</pre>
        <p><code>注意：</code>旧版本的<code>getParam</code>/<code>getPost</code>/<code>getGet</code>效果与上面的一致，但已不建议使用</p>

        <h2 id="router-check">权限验证</h2>
        <p>框架中提供了一套完整的权限验证逻辑，可对路由下所有<code>method</code>进行权限验证</p>
        <p>用户需要在action中添加<code>privilege</code>方法，具体返回字段如下</p>
        <pre class="code"><sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>private</sys> <prm>$key</prm> = <str>'test'</str>;

    <sys>protected function</sys> <act>privilege</act>()
    {
        <sys>return array</sys>(
            <note>// 登录验证（在privilegeService中定义）</note>
            <str>'login_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => <str>'*'</str>, <note>// 绑定action，*为所有method</note>
                <str>'params'</str> => [],   <note>// 传参（能获取到$this，不用另外传）可不传</note>
                <str>'callBack'</str> => [], <note>// 验证失败回调函数， 可不传</note>
            ),
            <str>'my_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => [<str>'index'</str>], <note>// 对action_index进行验证</note>
                <str>'params'</str> => [<prm>$this</prm>-><prm>key</prm>],   <note>// 传参</note>
                <str>'callBack'</str> => [<prm>$this</prm>, <str>'test'</str>], <note>// 验证失败后调用$this->test()</note>
            ),
        );
    }
    <note>// 根据逻辑被调用前会分别进行login_required和my_required验证，都成功后进入该方法</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// do something</note>
    }
    <note>// my_required验证失败后调用, $action为验证失败的action（这里是$this）</note>
    <sys>public function</sys> <act>test</act>(<prm>$action</prm>, <prm>$error</prm>)
    {
        <note>// do something</note>
    }
}</pre>

        <p>然后在<code>privilegeService</code>中定义验证方法</p>
        <pre class="code"><note>第一个参数$action为testAction，$key为params传入参数</note>
<sys>public function</sys> <act>my_required</act>(<prm>$action</prm>, <prm>$key</prm>=<sys>NULL</sys>)
{
    <sys>if</sys>(<prm>$key</prm>){
        <note>// 通过校验</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>(); <note>// 等同于 return true;</note>
    } <sys>else</sys> {
        <note>// 校验失败，错误信息可通过$this->privilegeService->getError()获取</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'key not exist'</str>);
    }
}</pre>

        <p><code>callBack</code>参数为校验失败时调用的方法，默认不填会抛出错误异常，程序不会再继续执行。</p>

        <p>如果需要不同路由都使用同一个验证方法，而分别传入不同参数验证，可以使用<code>requires</code>参数，用法参考下例：</p>
<pre class="code"><sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>protected function</sys> <act>privilege</act>()
    {
        <sys>return array</sys>(
            <str>'privilege_required'</str> => <sys>array</sys>(
                <note>// 根据不同路由传入相应操作权限</note>
                <str>'requires'</str> => [
                    [<str>'actions'</str>=>[<str>'index'</str>, <str>'view'</str>], <str>'params'</str>=>[TXPrivilege::<prm>user</prm>]],
                    [<str>'actions'</str>=>[<str>'edit'</str>, <str>'delete'</str>], <str>'params'</str>=>[TXPrivilege::<prm>admin</prm>]],
                ],
                <str>'callBack'</str> => [<prm>$this</prm>, <str>'test'</str>], <note>// 验证失败后调用$this->test()</note>
            ),
        );
    }

<note>// privilegeService</note>
<sys>public function</sys> <act>privilege_required</act>(<prm>$action</prm>, <prm>$privilege</prm>)
{
    <sys>if</sys>(TXApp::<prm>$model</prm>-><prm>person</prm>-><func>hasPrivilege</func>(<prm>$privilege</prm>)){
        <note>// 该用户有相应权限</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>(); <note>// 等同于 return true;</note>
    } <sys>else</sys> {
        <note>// 校验失败，错误信息可通过$this->privilegeService->getError()获取</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'forbidden'</str>);
    }
}</pre>

<p><code>注意：</code>使用<code>requires</code>参数时，<code>actions</code>和<code>params</code>参数将被覆盖</p>

    </div>

    <div class="bs-docs-section">
        <h1 id="config" class="page-header">配置</h1>
        <p>程序配置分两块，一块是系统配置，一块是程序配置</p>
        <p><code>/config/</code> 系统配置路径</p>
        <p><code>/app/config/</code> 程序逻辑配置路径</p>

        <h2 id="config-system">系统配置</h2>
        <p><code>/config/config.php</code> 系统基本配置（包括默认路由，自定义路由配置等）</p>
        <pre class="code"><sys>return array</sys>(
    <note>//路由配置</note>
    <str>'router'</str> => <sys>array</sys>(
        <str>'base_action'</str> => <str>'demo'</str>, <note>//默认路由入口</note>
        <str>'base_shell'</str> => <str>'index'</str>, <note>//默认shell入口</note>

        <note>//静态化配置</note>
        <str>'routeRule'</str> => <sys>array</sys>(
            <note>// test/123 => test/view</note>
            <str>'test/&lt;id:[\w_]+>'</str> => <str>'test/view'</str>,
            <note>// abc/test/123 => test/abc</note>
            <str>'&lt;method:\w+>/test/&lt;id:\d+>.html'</str> => <str>'test/&lt;method>'</str>,
        ),
    ),

    <note>//自动加载配置</note>
    <str>'autoload'</str> => <sys>array</sys>(
        <str>'autoPath'</str> => <str>'config/autoload.php'</str>,
        <note>//重新构建间隔时间s</note>
        <str>'autoSkipLoad'</str> => 5,
        <str>'autoThrow'</str> => <sys>true</sys>, <note>//使用外部autoload机制(如composer) 需设置为false</note>
    ),

    <note>//请求配置</note>
    <str>'request'</str> => <sys>array</sys>(
        <str>'trueToken'</str> => <str>'biny-csrf'</str>,
        <str>'csrfToken'</str> => <str>'csrf-token'</str>,
        <str>'csrfPost'</str> => <str>'_csrf'</str>,
        <str>'csrfHeader'</str> => <str>'X-CSRF-TOKEN'</str>,

        <note>// 约定userIP字段 X_REAL_IP</note>
        <str>'userIP'</str> => <str>''</str>,
        <note>// 强制返回页面协议</note>
        <str>'showTpl'</str> => <str>'X_SHOW_TEMPLATE'</str>,
        <note>//csrf白名单</note>
        <str>'csrfWhiteIps'</str> => <sys>array</sys>(
            <str>'127.0.0.1/24'</str>
        ),
        <note>//多语言cookie字段</note>
        <str>'languageCookie'</str> => <str>'biny_language'</str>
    ),

    <note>//响应配置</note>
    <str>'response'</str> => <sys>array</sys>(
        <str>'jsonContentType'</str> => <str>'application/json'</str>,
        <note>//兼容老版本 新版本都用one就可以了</note>
        <str>'paramsType'</str> => <str>'one'</str>,  <note>// one or keys</note>
        <note>// 以下配置在paramsType == one 时有效</note>
        <str>'paramsKey'</str> => <str>'PRM'</str>,
        <str>'objectEncode'</str> => <sys>true</sys>, <note>//object对象是否转义</note>
    ),

    <note>//日志相关配置</note>
    <str>'logger'</str> => <sys>array</sys>(
        <note>// 是否记录日志文件</note>
        <str>'files'</str> => <sys>true</sys>,
        <note>// 自定义日志记录方法
//        'sendLog' => array('TXCommon', 'sendLog'),
        // 自定义日志错误方法
//        'sendError' => array('TXCommon', 'sendError'),
        // 错误级别 NOTICE以上都会记录</note>
        <str>'errorLevel'</str> => <const>NOTICE</const>,
        <note>// 慢查询阀值(ms)</note>
        <str>'slowQuery'</str> => 1000,
    ),

    <note>// 数据库相关配置</note>
    <str>'database'</str> => <sys>array</sys>(
        <str>'returnIntOrFloat'</str> => <sys>true</sys>, <note>// 是否返回int或者float类型</note>
        <str>'returnAffectedRows'</str> => <sys>false</sys>, <note>// 是否返回受影响行数，false下返回成功true/失败false, true情况下-1为失败</note>
    ),

    <note>//缓存相关配置</note>
    <str>'cache'</str> => <sys>array</sys>(
        <str>'pkCache'</str> => <str>'tb:%s'</str>,
        <str>'session'</str> => <sys>array</sys>(
            <str>'save_handler'</str>=><str>'files'</str>,  <note>//redis memcache</note>
            <str>'maxlifetime'</str> => 86400    <note>//过期时间s</note>
        ),
        <note>// 开启redis自动序列化存储</note>
        <str>'serialize'</str> => <sys>true</sys>,
    ),

    <note>//异常配置</note>
    <str>'exception'</str> => <sys>array</sys>(
        <note>//返回页面</note>
        <str>'exceptionTpl'</str> => <str>'error/exception'</str>,
        <str>'errorTpl'</str> => <str>'error/msg'</str>,

        <str>'messages'</str> => <sys>array</sys>(
            500 => <str>'网站有一个异常，请稍候再试'</str>,
            404 => <str>'您访问的页面不存在'</str>,
            403 => <str>'权限不足，无法访问'</str>
        )
    ),



)</pre>
        <p><code>/config/autoload.php</code> 系统自动加载类的配置，会根据用户代码自动生成，无需配置，但必须具有<code>写权限</code></p>
        <p><code>/config/exception.php</code> 系统异常配置类</p>
        <p><code>/config/http.php</code> HTTP请求基本错误码</p>
        <p><code>/config/database.php</code> DAO映射配置</p>
        <p>用户可通过<code>TXApp::$base->config->get</code>方法获取</p>
        <p>简单例子：</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'session_name'</str> => <str>'biny_sessionid'</str>
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'session_name'</str>, <str>'config'</str>, <sys>true</sys>);</pre>

        <h2 id="config-app">程序配置</h2>
        <p>程序配置目录在<code>/app/config/</code>中</p>
        <p>默认有<code>dns.php</code>（连接配置） 和 <code>config.php</code>（默认配置路径）</p>
        <p>使用方式也与系统配置基本一致</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'memcache'</str> => <sys>array</sys>(
        <str>'host'</str> => <str>'10.1.163.35'</str>,
        <str>'port'</str> => 12121
    )
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'memcache'</str>, <str>'dns'</str>);</pre>

        <h2 id="config-env">环境配置</h2>
        <p>系统对不同环境的配置是可以做区分的</p>
        <p>系统配置在<code>/web/index.php</code>中</p>
        <pre class="code"><note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);</pre>

        <p>当程序调用<code>TXApp::$base->config->get</code>时，系统会自动查找对应的配置文件</p>
        <pre class="code"><note>// 当前环境dev 会自动查找 /config/config_dev.php文件</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'test'</str>, <str>'config'</str>);

<note>// 当前环境pub 会自动查找 /config/dns_pub.php文件</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'test2'</str>, <str>'dns'</str>);</pre>

        <p>公用配置文件可以放在不添加环境名的文件中，如<code>/config/config.php</code></p>
        <p>在系统中同时存在<code>config.php</code>和<code>config_dev.php</code>时，带有环境配置的文件内容会覆盖通用配置</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns'</str>,
    <str>'demo'</str> => <str>'dns'</str>,
}

<note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns_dev</str>
}

<note>// 返回 'dns_dev' </note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'test'</str>, <str>'dns'</str>);

<note>// 返回 'dns' </note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'demo'</str>, <str>'dns'</str>);</pre>
        <p>系统配置和程序配置中的使用方法相同</p>

        <h2 id="config-alias">别名使用</h2>
        <p>配置中是支持别名的使用的，在别名两边加上<code>@</code>即可</p>
        <p>系统默认有个别名 <code>web</code>会替换当前路径</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/'</str>
}

<note>// 返回 '/biny/my-path/' </note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>);</pre>

        <p>用户也可以自定义别名，例如</p>
        <pre class="code"><note>// config->get 之前执行</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>setAlias</func>(<str>'time'</str>, <sys>time</sys>());

<note>// config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/?time=@time@'</str>
}

<note>// 返回 '/biny/my-path/?time=1461141347'</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>);

<note>// 返回 '@web@/my-path/?time=@time@'</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>, <str>'config'</str>, <sys>false</sys>);</pre>

        <p>当然如果需要避免别名转义，也可以在<code>TXApp::$base->config->get</code>第三个参数传<code>false</code>，就不会执行别名转义了。</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="dao" class="page-header">数据库使用</h1>
        <p>框架要求每个数据库表都需要建一个单独的类，放在<code>/dao</code>目录下。跟其他目录一样，支持多层文件结构，写在子目录或孙目录中，但类名<code>必须唯一</code>。</p>
        <p>所有传入DAO 方法的参数都会自动进行<code>转义</code>，可以完全避免<code>SQL注入</code>的风险</p>
        <p>例如：</p>
        <pre class="code"><note>// testDAO.php 与类名保持一致</note>
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <note>// 链接库 数组表示主库从库分离：['database', 'slaveDb'] 对应dns里配置 默认为'database'</note>
    <sys>protected</sys> <prm>$dbConfig</prm> = <str>'database'</str>;
    <note>// 表名</note>
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// 键值 多键值用数组表示：['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// 是否使用数据库键值缓存，默认false</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;

    <note>// 分表逻辑，默认为表名直接加上分表id</note>
    <sys>public function</sys> <act>choose</act>(<prm>$id</prm>)
    {
        <prm>$sub</prm> = <prm>$id</prm> <sys>%</sys> 100;
        <prm>$this</prm>-><func>setDbTable</func>(<sys>sprintf</sys>(<str>'%s_%02d'</str>, <prm>$this</prm>-><prm>table</prm>, <prm>$sub</prm>));
        <sys>return</sys> <prm>$this</prm>;
    }
}</pre>



        <h2 id="dao-connect">连接配置</h2>
        <p>数据库库信息都配置在<code>/app/config/dns.php</code>中，也可根据环境配置在<code>dns_dev.php</code>/<code>dns_pre.php</code>/<code>dns_pub.php</code>里面</p>
        <p>基本参数如下：</p>
        <pre class="code"><note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'database'</str> => <sys>array</sys>(
        <note>// 库ip</note>
        <str>'host'</str> => <str>'127.0.0.1'</str>,
        <note>// 库名</note>
        <str>'database'</str> => <str>'Biny'</str>,
        <note>// 用户名</note>
        <str>'user'</str> => <str>'root'</str>,
        <note>// 密码</note>
        <str>'password'</str> => <str>'pwd'</str>,
        <note>// 编码格式</note>
        <str>'encode'</str> => <str>'utf8'</str>,
        <note>// 端口号</note>
        <str>'port'</str> => 3306,
        <note>// 是否长链接（默认关闭）</note>
        <str>'keep-alive'</str> => true,
    )
)</pre>
        <p>这里同时也可以配置多个，只需要在DAO类中指定该表所选的库即可（默认为<code>'database'</code>）</p>


        <h2 id="dao-mapped">DAO映射</h2>
        <p>上诉DAO都需要写PHP文件，框架这边也提供了一个简易版的映射方式</p>
        <p>用户可在<code>/config/database.php</code>中配置，示例如下</p>
        <pre class="code"><note>// database.php</note>
<sys>return array</sys>(
    <str>'dbConfig'</str> => array(
        <note>// 相当于创建了一个testDAO.php</note>
        <str>'test'</str> => <str>'Biny_Test'</str>
    )
);</pre>
        <p>然后就可以在<code>Action、Service、Model</code>各层中使用<code>testDAO</code>了</p>

<pre class="code"><note>// testAction.php
<sys>namespace</sys> app\controller;
/**
* DAO 或者 Service 会自动映射 生成对应类的单例
* @property \biny\lib\TXSingleDAO $testDAO
*/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 此处的testDAO为映射生成的，没有baseDAO中对于缓存的操作
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
    }
}</pre>
        <p>需要<code>注意</code>的是，映射的DAO不具备设置数据库功能（主从库都是默认的<code>database</code>配置）</p>
        <p>也不具备缓存操作（<code>getByPK、updateByPK、deleteByPK</code>等）的功能</p>
        <p>如果需要使用上述功能，还是需要在<code>dao</code>目录下创建php文件自定义相关参数</p>

        <h2 id="dao-simple">基础查询</h2>
        <p>DAO提供了<code>query</code>，<code>find</code>等基本查询方式，使用也相当简单</p>
        <pre class="code"><note>// testAction.php
<sys>namespace</sys> app\controller;
/**
 * DAO 或者 Service 会自动映射 生成对应类的单例
 * @property \app\dao\testDAO $testDAO
 */</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 返回 testDAO所对应表的全部内容 格式为二维数组
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
        <note>// 第一个参数为返回的字段 [['id'=>1, 'name'=>'xx'], ['id'=>2, 'name'=>'yy']]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>));
        <note>// 第二个参数返回键值，会自动去重 [1 => ['id'=>1, 'name'=>'xx'], 2 => ['id'=>2, 'name'=>'yy']]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>), <str>'id'</str>);

        <note>// 返回 表第一条数据 格式为一维 ['id'=>1, 'name'=>'xx', 'type'=>2]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>();
        <note>// 参数为返回的字段名 可以为字符串或者数组 ['name'=>'xx']</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>('name');
    }
}</pre>
        <p>同时还支持<code>count</code>，<code>max</code>，<code>sum</code>，<code>min</code>，<code>avg</code>等基本运算，count带参数即为<code>参数去重后数量</code></p>
        <pre class="code"><note>// count(*) 返回数量</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>();
<note>// count(distinct `name`) 返回去重后数量</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>(<str>'name'</str>);
<note>// max(`id`)</note>
<prm>$max</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>max</func>(<str>'id'</str>);
<note>// min(`id`)</note>
<prm>$min</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>min</func>(<str>'id'</str>);
<note>// avg(`id`)</note>
<prm>$avg</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>avg</func>(<str>'id'</str>);
<note>// sum(`id`)</note>
<prm>$sum</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>sum</func>(<str>'id'</str>);
</pre>
        <p>这里运算都为简单运算，需要用到复合运算或者多表运算时，建议使用<code>addition</code>方法</p>

        <p id="update28"><code>==============v2.8更新分割线=============</code></p>

        <p>Biny2.8.1之后添加了<code>pluck</code>（快速拉取列表）具体用法如下：</p>
<pre class="code"><note>// ['test1', 'test2', 'test3']</note>
<prm>$list</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>5))-><func>pluck</func>(<str>'name'</str>);
<note>// 同样也可以运用到多联表中，</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>5),
    ));
<note>// 如果所使用字段在多表中重复会报错</note>
<prm>$list</prm> = <prm>$filter</prm>-><func>pluck</func>(<str>'name'</str>);
<note>// 如果所使用字段在多表中重复出现需要指明所属的表</note>
<prm>$list</prm> = <prm>$filter</prm>-><func>pluck</func>(<sys>array</sys>(<str>'project'</str>=><str>'name'</str>));
</pre>

        <p>Biny2.8.1之后还添加了<code>paginate</code>（自动分页）方法，具体用法如下：</p>
<pre class="code"><note>// 返回一个以10条数据为一组的二维数组</note>
<prm>$results</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>5))-><func>paginate</func>(10);
<note>// 同样也可以运用到多联表中，</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>5),
    ));
<note>// 第二个参数默认为null，非null返回第n+1页（计数从0开始）的内容</note>
<note>// 第三个参数等同于fields的用法，为筛选的字段集合</note>
<prm>$results</prm> = <prm>$filter</prm>-><func>paginate</func>(10, 3, <sys>array</sys>(<sys>array</sys>(<str>'project'</str>=><str>'id'</str>, <str>'name'</str>));
</pre>

        <h2 id="dao-update">删改数据</h2>
        <p>在单表操作中可以用到删改数据方法，包括<code>update</code>（多联表也可），<code>delete</code>，<code>add</code>等</p>
        <p><code>update</code>方法为更新数据，返回成功（<code>true</code>）或者失败（<code>false</code>），条件内容参考后面<code>选择器</code>的使用</p>
<pre class="code"><note>// update `DATABASE`.`TABLE` set `name`='xxx', `type`=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'xxx'</str>, <str>'type'</str>=>5));</pre>

        <p><code>delete</code>方法返回成功（<code>true</code>）或者失败（<code>false</code>），条件内容参考后面<code>选择器</code>的使用</p>
<pre class="code"><note>// delete from `DATABASE`.`TABLE`</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>delete</func>();</pre>

        <p><code>add</code>方法 insert成功时默认返回数据库新插入自增ID，第二个参数为<code>false</code>时 返回成功（<code>true</code>）或者失败（<code>false</code>）</p>
<pre class="code"><note>// insert into `DATABASE`.`TABLE` (`name`,`type`) values('test', 1)</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<note>// false 时返回true/false</note>
<prm>$id</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>add</func>(<prm>$sets</prm>, <sys>false</sys>);</pre>

        <p>框架同时也提供了受影响行数的返回，可以在<code>/config/config.php</code>中，将字段<code>returnAffectedRows</code>置为<code>true</code>即可</p>

        <p><code>addCount</code>方法返回成功（<code>true</code>）或者失败（<code>false</code>），相当于<code>update set count = count+n</code></p>
<pre class="code"><note>// update `DATABASE`.`TABLE` set `type`=`type`+5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addCount</func>(<sys>array</sys>(<str>'type'</str>=>5);</pre>

        <p><code>注意：</code>新版本addCount方法可以被update方法替代，目前暂时还保留，但已<code>不建议使用</code>。使用方法如下：</p>

        <pre class="code"><note>// update `DATABASE`.`TABLE` set `type`=`type`+5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>([<str>'type'</str>=>[<str>'+'</str>=>5]]);
<note>// update `DATABASE`.`TABLE` set `type`=`count`-`num`-4</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>([<str>'type'</str>=>[<str>'-'</str>=>[<str>'count'</str>, <str>'num'</str>, 4]]]);
        </pre>

        <p><code>createOrUpdate</code>方法 为添加数据，但当有重复键值时会自动update数据</p>
<pre class="code"><note>// 第一个参数为insert数组，第二个参数为失败时update参数，不传即为第一个参数</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>createOrUpdate</func>(<prm>$sets</prm>);</pre>

        <p><code>addList</code>方法为批量添加数据，第二个参数为批量执行的个数，默认一次执行100行<br />返回成功（<code>true</code>）或者失败（<code>false</code>）</p>
<pre class="code"><note>// 参数为批量数据值（二维数组），键值必须统一</note>
<prm>$sets</prm> = <sys>array</sys>(
    <sys>array</sys>(<str>'name'</str>=><str>'test1'</str>, <str>'type'</str>=>1),
    <sys>array</sys>(<str>'name'</str>=><str>'test2'</str>, <str>'type'</str>=>2),
);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addList</func>(<prm>$sets</prm>);</pre>

        <p>Biny 2.8.7之后，支持<code>insert</code>方法，作用等同于<code>add</code></p>
        <p><code>addList</code>支持replace into逻辑，第三个参数为<code>true</code>时，会以replace into 逻辑执行</p>
<pre class="code"><note>// REPLACE INTO TABLE ...</note>
<prm>$sets</prm> = <sys>array</sys>(
    <sys>array</sys>(<str>'name'</str>=><str>'test1'</str>, <str>'type'</str>=>1),
    <sys>array</sys>(<str>'name'</str>=><str>'test2'</str>, <str>'type'</str>=>2),
);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addList</func>(<prm>$sets</prm>, 100, <sys>true</sys>);</pre>

        <h2 id="dao-join">多联表</h2>
        <p>框架支持多连表模型，DAO类都有<code>join</code>（全联接），<code>leftJoin</code>（左联接），<code>rightJoin</code>（右联接）方法</p>
        <p>参数为联接关系</p>
        <pre class="code"><note>// on `user`.`projectId` = `project`.`id` and `user`.`type` = `project`.`type`</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>, <str>'type'</str>=><str>'type'</str>));</pre>

        <p><code>$DAO</code>可以继续联接，联接第三个表时，联接关系为二维数组，第一个数组对应第一张表与新表关系，第二个数组对应第二张表与新表关系</p>
        <pre class="code"><note>// on `user`.`testId` = `test`.`id` and `project`.`type` = `test`.`status`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>leftJoin</func>(<prm>$this</prm>-><prm>testDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(<str>'testId'</str>=><str>'id'</str>),
    <sys>array</sys>(<str>'type'</str>=><str>'status'</str>)
));</pre>

        <p>可以继续联接，联接关系同样为二维数组，三个对象分别对应原表与新表关系，无关联则为空，最后的空数组可以<code>省略</code></p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(),
    <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
<note>//  array()</note>
));</pre>
        <p>以此类推，理论上可以建立任意数量的关联表</p>

        <p>参数有两种写法，上面那种是位置对应表，另外可以根据<code>别名</code>做对应，<code>别名</code>即DAO之前的字符串</p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name` and `user`.`mId` = `message`.`id`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <str>'project'</str> => <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
    <str>'user'</str> => <sys>array</sys>(<str>'mId'</str>=><str>'id'</str>),
));</pre>


        <p>多联表同样可以使用<code>query</code>，<code>find</code>，<code>count</code>等查询语句。参数则改为<code>二维数组</code>。</p>
        <p>和联表参数一样，参数有两种写法，一种是位置对应表，另一种即<code>别名</code>对应表，同样也可以混合使用。</p>
        <pre class="code"><note>// SELECT `user`.`id` AS 'uId', `user`.`cash`, `project`.`createTime` FROM ...</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>query</func>(<sys>array</sys>(
      <sys>array</sys>(<str>'id'</str>=><str>'uId'</str>, <str>'cash'</str>),
      <str>'project'</str> => <sys>array</sys>(<str>'createTime'</str>),
    ));</pre>

        <p>联表条件中有时需要用到等于固定值的情况，可以通过<code>on</code>方法添加</p>
        <pre class="code"><note>// ... on `user`.`projectId` = `project`.`id` and `user`.`type` = 10 and `project`.`cash` > 100</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>on</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>10),
        <sys>array</sys>(<str>'cash'</str>=><sys>array</sys>(<str>'>'</str>, 100)),
    ))-><func>query</func>();</pre>

        <p>多联表的查询和修改（<code>update</code>），和单表操作基本一致，需要注意的是单表参数为<code>一维数组</code>，多表则为<code>二维数组</code>，写错会导致执行失败。</p>

        <p><code>注意：</code>多联表中的选择器应该使用二维数组，例如：</p>
        <pre class="code"><note>// ... where `user`.`type` = 10 and `project`.`cash` = 100</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>10),
        <sys>array</sys>(<str>'cash'</str>=>100),
    ))-><func>query</func>();</pre>

        <p>具体选择器使用请参考选择器文档内容。</p>

        <p>Biny 2.8.6之后<code>join/leftJoin/rightJoin</code>可以在第一张表添加选择器后再使用，使用方法如下：</p>
        <pre class="code"><note>// ... where `user`.`type` = 10</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>10))
    -><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>query</func>();
<note>// 等同于下方原来的写法，这样在第一张表中参数会自动带入到联表参数中</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>10),
    ))-><func>query</func>();</pre>


        <h2 id="dao-filter">选择器</h2>

        <p>DAO类都可以调用<code>filter</code>（与选择器），<code>merge</code>（或选择器），效果相当于筛选表内数据</p>
        <p>同样选择器支持单表和多表操作，参数中单表为<code>一维数组</code>，多表则为<code>二维数组</code></p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `user`.`type` = 'admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>而用<code>merge</code>或选择器筛选，条件则用<code>or</code>相连接</p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 OR `user`.`type` = 'admin'</note>
<prm>$merge</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>同样多表参数也可用<code>别名</code>对应表，用法跟上面一致，这里就不展开了</p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `project`.`type` = 'outer'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'id'</str>=><str>1</str>),
        <sys>array</sys>(<str>'type'</str>=><str>'outer'</str>),
    ));</pre>

        <p><code>$filter</code>条件可以继续调用<code>filter</code>/<code>merge</code>方法，条件会在原来的基础上继续筛选</p>
        <pre class="code"><note>// ... WHERE (...) OR (`user`.`name` = 'test')</note>
<prm>$filter</prm> = <prm>$filter</prm>-><func>merge</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>);</pre>

        <p><code>$filter</code>条件也可以作为参数传入<code>filter</code>/<code>merge</code>方法。效果为条件的叠加。</p>
        <pre class="code"><note>// ... WHERE (`user`.`id` = 1 AND `user`.`type` = 'admin') OR (`user`.`id` = 2 AND `user`.`type` = 'user')</note>
<prm>$filter1</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);
<prm>$filter2</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>2, <str>'type'</str>=><str>'user'</str>));
<prm>$merge</prm> = <prm>$filter1</prm>-><func>merge</func>(<prm>$filter2</prm>);</pre>

        <p>无论是<code>与选择器</code>还是<code>或选择器</code>，条件本身作为参数时，条件自身的<code>DAO</code>必须和被选择对象的<code>DAO</code>保持一致，否者会抛出<code>异常</code></p>

        <p>值得注意的是<code>filter</code>和<code>merge</code>的先后顺序对条件筛选是有影响的</p>
        <p>可以参考下面这个例子</p>
        <pre class="code"><note>// WHERE (`user`.`id`=1 AND `user`.`type`='admin') OR `user`.`id`=2</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>)-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2));

<note>// WHERE `user`.`id`=2 AND (`user`.`id`=1 AND `user`.`type`='admin')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2))-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);</pre>

        <p>由上述例子可知，添加之间关联符是跟<code>后面</code>的选择器表达式<code>保持一致</code></p>

        <p><code>选择器</code>获取数据跟<code>DAO</code>方法一致，单表的<code>选择器</code>具有单表的所有查询，删改方法，而多表的<code>选择器</code>具有多表的所有查询，修改方法</p>
        <pre class="code"><note>// UPDATE `DATABASE`.`TABLE` AS `user` SET `user`.`name` = 'test' WHERE `user`.`id` = 1</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1)-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>));

<note>// SELECT * FROM ... WHERE `project`.`type` = 'admin'</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(<sys>array</sys>(),<sys>array</sys>(<str>'type'</str>=><str>'admin'</str>)))
    -><func>query</func>();</pre>

        <p>另外，如果想实现<code>where start=end</code>或者<code>where start=end+86400</code>这类的条件也是支持的，方法如下：</p>
        <pre class="code"><note>// ... WHERE `user`.`lastLoginTime` = `user`.`registerTime` and `user`.`lastLoginTime` <= refreshTime+86400</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'lastLoginTime'</str>=>TXDatabase::<func>field</func>(<str>'`user`.`registerTime`'</str>),
    <str>'<='</str>=><sys>array</sys>(<str>'lastLoginTime'</str>=>TXDatabase::<func>field</func>(<str>'refreshTime+86400'</str>)),
));</pre>

        <p>无论是<code>filter</code>还是<code>merge</code>，在执行SQL语句前都<code>不会被执行</code>，不会增加sql负担，可以放心使用。</p>

        <h2 id="dao-extracts">复杂选择</h2>
        <p>除了正常的匹配选择以外，<code>filter</code>，<code>merge</code>里还提供了其他复杂选择器。</p>
        <p>如果数组中值为<code>数组</code>的话，会自动变为<code>in</code>条件语句</p>
        <pre class="code"><note>// WHERE `user`.`type` IN (1,2,3,'test')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));</pre>

        <p>其他还包括 <code>></code>，<code><</code>，<code>>=</code>，<code><=</code>，<code>!=</code>，<code><></code>，<code>is</code>，<code>is not</code>
            ，同样，多表的情况下需要用<code>二维数组</code>去封装</p>
        <pre class="code"><note>// WHERE `user`.`id` >= 10 AND `user`.`time` >= 1461584562 AND `user`.`type` is not null</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'>='</str>=><sys>array</sys>(<str>'id'</str>=>10, <str>'time'</str>=>1461584562),
    <str>'is not'</str>=><sys>array</sys>(<str>'type'</str>=><sys>NULL</sys>),
));

<note>// WHERE `user`.`id` != 3 AND `user`.`id` != 4 AND `user`.`id` != 5</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'!='</str>=><sys>array</sys>(<str>'id'</str>=><sys>array</sys>(3, 4, 5))
));</pre>

        <p>另外，<code>like语句</code>也是支持的，可匹配正则符的开始结尾符，具体写法如下：</p>
        <pre class="code"><note>// WHERE `user`.`name` LIKE '%test%' OR `user`.`type` LIKE 'admin%' OR `user`.`type` LIKE '%admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(
    <str>'__like__'</str>=><sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=><str>'^admin'</str>, <str>'type'</str>=><str>'admin$'</str>),
));

<note>// WHERE `user`.`name` LIKE '%test%' OR `user`.`name` LIKE 'admin%' OR `user`.`name` LIKE '%demo'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(
    <str>'__like__'</str>=><sys>array</sys>(
        <str>'name'</str>=><sys>array</sys><str>('test'</str>, <str>'^admin'</str>, <str>'demo$'</str>),
    )
));</pre>

        <p><code>not in</code>语法暂时并未支持，可以暂时使用多个<code>!=</code>或者<code><></code>替代</p>

        <p>同时<code>filter/merge</code>也可以被迭代调用，以应对不确定筛选条件的复杂查询</p>
        <pre class="code"><note>// 某一个返回筛选数据的Action</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>;
<sys>if </sys>(<prm>$status</prm>=<prm>$this</prm>-><func>param</func>(<str>'status'</str>)){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'status'</str>=><prm>$status</prm>));
}
<sys>if </sys>(<prm>$startTime</prm>=<prm>$this</prm>-><func>param</func>(<str>'start'</str>, 0)){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'start'</str>=><prm>$startTime</prm>)));
}
<sys>if </sys>(<prm>$endTime</prm>=<prm>$this</prm>-><func>param</func>(<str>'end'</str>, <func>time</func>())){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'<'</str>=><sys>array</sys>(<str>'end'</str>=><prm>$endTime</prm>)));
}
<note>// 获取复合条件数量</note>
<prm>$count</prm> = <prm>$DAO</prm>-><func>count</func>();
<note>// 获取复合条件前10条数据</note>
<prm>$data</prm> = <prm>$DAO</prm>-><func>limit</func>(10)-><func>query</func>();</pre>

        <h2 id="dao-group">其他条件</h2>
        <p>在<code>DAO</code>或者<code>选择器</code>里都可以调用条件方法，方法可传递式调用，相同方法内的条件会自动合并</p>
        <p>其中包括<code>group</code>，<code>addition</code>，<code>order</code>，<code>limit</code>，<code>having</code></p>
        <pre class="code"><note>// SELECT `user`.`id`, avg(`user`.`cash`) AS 'a_c' FROM `TABLE` `user` WHERE ...
        GROUP BY `user`.`id`,`user`.`type` HAVING `a_c` >= 1000 ORDER BY `a_c` DESC, `id` ASC LIMIT 20,10;</note>
<prm>$this</prm>-><prm>userDAO</prm> <note>//->filter(...)</note>
    -><func>addition</func>(<sys>array</sys>(<str>'avg'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'a_c'</str>))
    -><func>group</func>(<sys>array</sys>(<str>'id'</str>, <str>'type'</str>))
    -><func>having</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'a_c'</str>=> 1000)))
    -><func>order</func>(<sys>array</sys>(<str>'a_c'</str>=><str>'DESC'</str>, <str>'id'</str>=><str>'ASC'</str>))
    <note>// limit 第一个参数为取的条数，第二个参数为起始位置（默认为0）</note>
    -><func>limit</func>(10, 20)
    -><func>query</func>(<sys>array</sys>(<str>'id'</str>));</pre>

        <p><code>addition</code>是对数据做计算处理的方法，提供了<code>max</code>，<code>count</code>，<code>sum</code>，<code>min</code>，<code>avg</code>等计算方法</p>
        <p>多联表时同样需要用到<code>二维数组</code></p>
        <pre class="code"><note>// SELECT avg(`user`.`cash`) AS 'a_c', avg(`user`.`time`) AS 'time',
        sum(`user`.`cash`) AS 'total', min(`test`.`testid`) AS 'testid'
        FROM `TABLE1` `user` join `TABLE2` `test` ON `user`.`id` = `test`.`user_id` WHERE ...
        GROUP BY `user`.`id`,`user`.`type` HAVING `a_c` >= 1000 ORDER BY `a_c` DESC, `id` ASC LIMIT 0,10;</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>testDAO</prm>, <sys>array</sys>(<str>'id'</str>=><str>'user_id'</str>))
<prm>$DAO</prm> <note>//->filter(...)</note>
    -><func>addition</func>(<sys>array</sys>(
        <sys>array</sys>(
            <str>'avg'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'a_c'</str>, <str>'time'</str>),
            <str>'sum'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'total'</str>),
        ),
        <sys>array</sys>(
            <str>'min'</str>=><sys>array</sys>(<str>'testid'</str>),
        ),
    )-><func>query</func>();</pre>

        <p>每次添加条件后都是独立的，<code>不会影响</code>原DAO 或者 选择器，可以放心的使用</p>

        <pre class="code"><note>// 这个对象不会因添加条件而变化</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));
<note>// 2</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>limit</func>(2)-><func>count</func>()
<note>// 4</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>count</func>()
<note>// 100 (user表总行数)</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>count</func>()</pre>

        <p>Biny同时也可以使用<code>TXDatabase::field()</code>来支持复杂的<code>Group By</code>语句，例如：</p>
        <pre class="code"><note>// SELECT FROM_UNIXTIME(time,'%Y-%m-%d') AS time, count(*) AS 'count'
                FROM `user` Group By FROM_UNIXTIME(time,'%Y-%m-%d')</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>group</func>(TXDatabase::<func>field</func>(<str>"FROM_UNIXTIME(time,'%Y-%m-%d')"</str>))
    -><func>addition</func>(<sys>array</sys>(<str>'count'</str>=><str>'*'</str>))
    -><func>query</func>(<str>"FROM_UNIXTIME(time,'%Y-%m-%d') AS time");</pre>

        <h2 id="dao-command">SQL模版</h2>
        <p>框架中提供了上述<code>选择器</code>，<code>条件语句</code>，<code>联表</code>等，基本覆盖了所有sql语法，但可能还有部分生僻的用法无法被实现，
        于是这里提供了一种SQL模版的使用方式，支持用户自定义SQL语句，但<code>并不推荐用户使用</code>，如果一定要使用的话，请务必自己做好<code>防SQL注入</code></p>

        <p>这里提供了两种方式，<code>select</code>（查询，返回数据），以及<code>command</code>（执行，返回bool）</p>
        <p>方法会自动替换<code>:where</code>,<code>:table</code>,<code>:order</code>,<code>:group</code>,<code>:addition</code>字段</p>
        <pre class="code"><note>// select * from `DATABASE`.`TABLE` WHERE ...</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select * from :table WHERE ...;'</str>);

<note>// update `DATABASE`.`TABLE` `user` set name = 'test' WHERE `user`.`id` = 10 AND type = 2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>10))
    -><func>command</func>(<str>"update :table set name = 'test' WHERE :where AND type = 2;"</str>);

<note>// select id,sum(`cash`) as 'cash' from `DATABASE`.`TABLE` WHERE `id`>10
    GROUP BY `type` HAVING `cash`>=100 ORDER BY `id` desc;</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'>'</str>=><sys>array</sys>(<str>'id'</str>=>10)))
    -><func>group</func>(<sys>array</sys>(<str>'type'</str>))-><func>having</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'cash'</str>=>100)))-><func>order</func>(<sys>array</sys>(<str>'id'</str>=><str>'desc'</str>))
    -><func>addition</func>(<sys>array</sys>(<str>'sum'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'cash'</str>)))
    -><func>select</func>(<str>'select id,:addition from :table WHERE :where :group :order;'</str>);</pre>

        <p>另外还可以添加一些自定义变量，这些变量会自动进行<code>sql转义</code>，防止<code>sql注入</code></p>
        <p>其中键值的替换符为<code>;</code>，例如<code>;key</code>，值的替换符为<code>:</code>，例如<code>:value</code></p>
        <pre class="code"><note>// select `name` from `DATABASE`.`TABLE` WHERE `name`=2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;key from :table WHERE ;key=:value;'</str>, <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=>2));</pre>

        <p>同时替换内容也可以是数组，系统会自动替换为以<code>,</code>连接的字符串</p>
        <pre class="code"><note>// select `id`,`name` from `DATABASE`.`TABLE` WHERE `name` in (1,2,3,'test')</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;fields from :table WHERE ;key in (:value);'</str>,
    <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>), <str>'fields'</str>=><sys>array</sys>(<str>'id'</str>, <str>'name'</str>)));</pre>

        <p>以上替换方式都会进行<code>SQL转义</code>，建议用户使用模版替换，而不要自己将变量放入SQL语句中，防止<code>SQL注入</code></p>

        <h2 id="dao-cursor">游标数据</h2>
        <p>如果DB中取出的数据非常大，而PHP中却无法承受这么大量的内存可以用来处理，这时候就需要用到<code>cursor</code>游标了</p>
        <p>游标可以将复合条件的数据逐一取出，在程序中进行分批处理，从而降低大数据所带来的内存瓶颈</p>
        <pre class="code"><note>// 选择器，条件类模式完全一样，在获取数据时使用cursor方法</note>
<prm>$rs</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))-><func>cursor</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>));
<note>// 通过 TXDatabase::step 逐个取出data数据，e.g: ['id'=>2, 'name'=>'test']</note>
<sys>while</sys> (<prm>$data</prm>=TXDatabase::<func>step</func>(<prm>$rs</prm>)){
    <note>do something...</note>
}</pre>
        <p>如果在游标数据中需要再使用其他sql语句，则需要在<code>cursor</code>方法中传第二个参数<code>false</code>，否则在cursor未执行完之前其他语句无法执行</p>
        <pre class="code"><note>// 选择器，条件类模式完全一样，在获取数据时使用cursor方法</note>
<prm>$rs</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))-><func>cursor</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>), <sys>false</sys>);
<note>// 通过 TXDatabase::step 逐个取出data数据，e.g: ['id'=>2, 'name'=>'test']</note>
<sys>while</sys> (<prm>$data</prm>=TXDatabase::<func>step</func>(<prm>$rs</prm>)){
    <note>// other sql...</note>
    <prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>();
}</pre>

        <p>如果使用SQL模版的话，也可以通过传递第三个参数<code>TXDatabase::FETCH_TYPE_CURSOR</code>来实现游标的使用</p>
        <pre class="code"><note>// 使用方法跟上诉方式一样</note>
<prm>$rs</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))
  -><func>select</func>(<str>'SELECT * FROM :table WHERE :where AND status=:status'</str>, <sys>array</sys>(<str>'status'</str>=>2), TXDatabase::<prm>FETCH_TYPE_CURSOR</prm>);
<note>// 通过 TXDatabase::step 逐个取出data数据，e.g: ['id'=>2, 'name'=>'test', 'type'=>1, 'status'=>2]</note>
<sys>while</sys> (<prm>$data</prm>=TXDatabase::<func>step</func>(<prm>$rs</prm>)){
    <note>do something...</note>
}</pre>

        <p>Biny 2.8.2之后<code>cursor</code>第二个参数可传匿名函数function作为数据回调使用，使用方法如下：</p>
        <pre class="code">
<prm>$result</prm> = <sys>array</sys>();
<note>// $data为迭代的数据，$index为索引</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))
  -><func>cursor</func>(<str>'*'</str>, <sys>function</sys>(<prm>$data</prm>, <prm>$index</prm>) <sys>use</sys>(&<prm>$result</prm>){
    <note>do something...</note>
});</pre>

        <h2 id="dao-transaction">事务处理</h2>
        <p>框架为DAO提供了一套简单的事务处理机制，默认是关闭的，可以通过<code>TXDatebase::start()</code>方法开启</p>
        <p><code>注意：</code>请确保连接的数据表是<code>innodb</code>的存储引擎，否者事务并不会生效。</p>

        <p>在<code>TXDatebase::start()</code>之后可以通过<code>TXDatebase::commit()</code>来进行完整事务的提交保存，但并不会影响<code>start</code>之前的操作</p>
        <p>同理，可以通过<code>TXDatebase::rollback()</code>进行整个事务的回滚，回滚所有当前未提交的事务</p>
        <p>当程序调用<code>TXDatebase::end()</code>方法后事务会全部终止，未提交的事务也会自动回滚，另外，程序析构时，也会自动回滚未提交的事务</p>

        <pre class="code"><note>// 在事务开始前的操作都会默认提交，num:0</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>0]);
<note>// 开始事务</note>
TXDatabase::<func>start</func>();
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<note>// 回滚事务</note>
TXDatabase::<func>rollback</func>();
<note>// 当前num还是0</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<note>// 提交事务</note>
TXDatabase::<func>commit</func>();
<note>// num = 2</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// 关闭事务</note>
TXDatabase::<func>end</func>();</pre>

        <p>另外，事务的开启并不会影响<code>select</code>操作，只对增加，删除，修改操作有影响</p>

        <h2 id="dao-cache">数据缓存</h2>
        <p>框架这边针对<code>pk键值索引</code>数据可以通过继承<code>baseDAO</code>进行缓存操作，默认为<code>关闭</code>，可在DAO中定义<code>$_pkCache = true</code>来开启</p>
        <p>然后需要在DAO中制定表键值，复合索引需要传<code>数组</code>，例如：<code>['id', 'type']</code></p>
        <p>因为系统缓存默认走<code>redis</code>，所以开启缓存的话，需要在<code>/app/config/dns_xxx.php</code>中配置环境相应的redis配置</p>
        <pre class="code"><note>// testDAO</note>
<sys>namespace</sys> app\dao;
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <sys>protected</sys> <prm>$dbConfig</prm> = [<str>'database'</str>, <str>'slaveDb'</str>];
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// 表pk字段 复合pk为数组 ['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// 开启pk缓存</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;
}</pre>

        <p><code>baseDAO</code>中提供了<code>getByPk</code>，<code>updateByPk</code>，<code>deleteByPk</code>方法，
            当<code>$_pkCache</code>参数为<code>true</code>时，数据会走缓存，加快数据读取速度。</p>

        <p><code>getByPk</code> 读取键值数据，返回一维数组数据</p>
        <pre class="code"><note>//参数为pk值 返回 ['id'=>10, 'name'=>'test', 'time'=>1461845038]</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>getByPk</func>(10);

<note>//复合pk需要传数组</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>getByPk</func>(<sys>array</sys>(10, <str>'test'</str>));</pre>

        <p><code>updateByPk</code> 更新单条数据</p>
        <pre class="code"><note>//参数为pk值,update数组，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>updateByPk</func>(10, <sys>array</sys>(<str>'name'</str>=><str>'test'</str>));</pre>

        <p><code>deleteByPk</code> 删除单条数据</p>
        <pre class="code"><note>//参数为pk值，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>deleteByPk</func>(10);</pre>

        <p><code>注意：</code>开启<code>$_pkCache</code>的DAO不允许再使用<code>update</code>和<code>delete</code>方法，这样会导致缓存与数据不同步的现象。</p>
        <p>如果该表频繁删改数据，建议关闭<code>$_pkCache</code>字段，或者在删改数据后调用<code>clearCache()</code>方法来清除缓存内容，从而与数据库内容保持同步。</p>


        <h2 id="dao-log">语句调试</h2>
        <p>SQL调试方法已经集成在框架事件中，只需要在需要调试语句的方法前调用<code>TXEvent::on(onSql)</code>就可以在<code>页面控制台</code>中输出sql语句了</p>
        <pre class="code"><note>// one方法绑定一次事件，输出一次后自动释放</note>
TXEvent::<func>one</func>(<const>onSql</const>);
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();

<note>// on方法绑定事件，直到off释放前都会有效</note>
TXEvent::<func>on</func>(<const>onSql</const>);
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
TXEvent::<func>off</func>(<const>onSql</const>);</pre>

        <p>该SQL事件功能还可自行绑定方法，具体用法会在后面<code>事件</code>介绍中详细展开</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="view" class="page-header">页面渲染</h1>
        <p>请在<code>php.ini</code>配置中打开<code>short_open_tag</code>，使用简写模版，提高开发效率</p>
        <p>页面view层目录在<code>/app/template/</code>下面，可以在<code>Action</code>层中通过<code>$this->display()</code>方法返回</p>
        <p>一般<code>Action</code>类都会继承<code>baseAction</code>类，在<code>baseAction</code>中可以将一些页面通用参数一起下发，减少开发，维护成本</p>

        <h2 id="view-param">渲染参数</h2>
        <p><code>display</code>方法有三个参数，第一个为指定<code>template</code>文件，第二个为页面参数数组，第三个为系统类数据(<code>没有可不传</code>)。</p>
        <pre class="code"><note>// 返回/app/template/main/test.tpl.php </note>
<sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <sys>array</sys>(<str>'test'</str>=>1), <sys>array</sys>(<str>'path'</str>=><str>'/test.png'</str>));

<note>/* /app/template/main/test.tpl.php
返回:
&lt;div class="container">
    &lt;span> 1  &lt;/span>
    &lt;img src="/test.png"/>
&lt;/div> */</note>
<act>&lt;div</act> class="<func>container</func>"<act>&gt;</act>
    <act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
    <act>&lt;img</act> src="<sys>&lt;?=</sys><prm>$path</prm><sys>?&gt;</sys>"<act>/&gt;</act>
<act>&lt;/div&gt;</act></pre>

        <p>第二个参数的数据都会放到<code>$PRM</code>这个页面对象中。第三个参数则会直接被渲染，适合<code>静态资源地址</code>或者<code>类数据</code></p>

        <h2 id="view-tkd">自定义TKD</h2>
        <p>页面TKD一般都默认在<code>common.tpl.php</code>定义好，如果页面单独需要修改对应的<code>title，keywords，description</code>的话，
            也可以在<code>TXResponse</code>生成后对其赋值</p>
        <pre class="code"><prm>$view</prm> = <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <prm>$params</prm>);
<prm>$view</prm>-><prm>title</prm> = <str>'Biny'</str>;
<prm>$view</prm>-><prm>keywords</prm> = <str>'biny,php,框架'</str>;
<prm>$view</prm>-><prm>description</prm> = <str>'一款轻量级好用的框架'</str>;
<sys>return</sys> <prm>$view</prm>;</pre>

        <h2 id="view-xss">反XSS注入</h2>
        <p>使用框架<code>display</code>方法，自动会进行参数<code>html实例化</code>，防止XSS注入。</p>
        <p><code>$PRM</code>获取参数时有两种写法，普通的数组内容获取，会自动进行<code>转义</code></p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>另外可以用私用参数的方式获取，则不会被转义，适用于需要显示完整页面结构的需求（<code>普通页面不推荐使用，隐患很大</code>）</p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &lt;div&gt; </note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><prm>test</prm><sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<note>// 效果同上</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><func>get</func>(<str>'test'</str>)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>在多层数据结构中，也一样可以递归使用</p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>][<str>'key1'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>get</func>(0)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>而多层结构数组参数会在使用时<code>自动转义</code>，不使用时则不会进行转义，避免资源浪费，影响渲染效率。</p>


        <p><code>注意：</code>第三个参数是否<code>html实例化</code>，可在<code>/config/config.php</code>中对字段<code>objectEncode</code>进行配置。</p>

        <h2 id="view-func">参数方法</h2>
        <p>渲染参数除了渲染外，还提供了一些原有<code>array</code>的方法，例如：</p>
        <p><code>in_array</code> 判断字段是否在数组中</p>
        <pre class="code"><note>// 等同于 in_array('value', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>in_array</func>(<str>'value'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p><code>array_key_exists</code> 判断key字段是否在数组中</p>
        <pre class="code"><note>// 等同于 array_key_exists('key1', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>array_key_exists</func>(<str>'key1'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>其他方法以此类推，使用方式是相同的，其他还有<code>json_encode</code></p>
        <pre><note>// 赋值给js参数 var jsParam = {'test':1, "demo": {"key": "test"}};</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>json_encode</func>()<sys>?&gt;</sys>;</pre>

        <p>判断数组参数是否为空，可以直接调用<code>$PRM['array']()</code>或者<code>$PRM（'array'）</code>方法判断，效果等同<code>!empty()</code>方法</p>
        <pre class="code"><note>// 等同于 if (!empty($array))</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>（<str>'array'</str>)) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>其他参数方法可以自行在<code>/lib/data/TXArray.php</code>中进行定义</p>
        <p>比如：定义一个<code>len</code>方法，返回数组长度</p>
        <pre class="code"><note>/lib/data/TXArray.php</note>
<sys>public function</sys> <act>len</act>()
{
    <sys>return count</sys>(<prm>$this</prm>-><prm>storage</prm>);
}</pre>
        <p>然后就可以在<code>tpl</code>中开始使用了</p>
        <pre><note>// 赋值给js参数 var jsParam = 2;</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>len</func>()<sys>?&gt;</sys>;</pre>

    </div>

    <div class="bs-docs-section">
        <h1 id="event" class="page-header">事件</h1>
        <p>框架中提供了事件机制，可以方便全局调用。其中系统默认已提供的有<code>beforeAction</code>，<code>afterAction</code>，<code>onException</code>，<code>onError</code>，<code>onSql</code>这几个</p>
        <p><code>beforeAction</code>为Action执行前执行的事件（在<code>init()</code>方法之后被触发）</p>
        <p><code>afterAction</code>为Action执行后执行的事件（会在渲染页面之前触发）</p>
        <p><code>onException</code>系统抛出异常时被触发，会传递错误code，在<code>/config/exception.php</code>中定义code</p>
        <p><code>onError</code>程序调用<code>$this->error($data)</code>方法时被触发，传递<code>$data</code>参数</p>
        <p><code>onSql</code>执行语句时被触发，上述例子中的<code>TXEvent::on(onSql)</code>就是使用了该事件</p>

        <h2 id="event-init">定义事件</h2>
        <p>系统提供了两种定义事件的方式，一种是定义长期事件<code>$fd = TXEvent::on($event, [$class, $method])</code>，直到被off之前都会生效。</p>
        <p>参数分别为<code>事件名</code>，<code>方法[类，方法名]</code> 方法可以不传，默认为<code>TXLogger::event()</code>方法，会在console中打印</p>
        <p><code>$fd</code>返回的是该事件的操作符。在调用off方法时，可以通过传递该操作符解绑该事件。</p>

        <pre class="code"><sys>namespace</sys> app\controller;
<note>/**
* 主页Action
* @property \app\service\testService $testService
*/  </note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//构造函数</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>// 要触发beforeAction事件，可在init里定义，会在init之后被触发</note>
        TXEvent::<func>on</func>(<const>beforeAction</const>, <sys>array</sys>(<prm>$this</prm>, <str>'test_event'</str>));
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 绑定testService里的my_event1方法 和 my_event2方法 到 myEvent事件中，两个方法都会被执行，按绑定先后顺序执行</note>
        <prm>$fd1</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event1'</str>));
        <prm>$fd2</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event2'</str>));

        <note>// do something ..... </note>

        <note>// 解绑myEvent事件的 my_event1方法</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>, <prm>$fd1</prm>);

        <note>// 解绑myEvent事件，所有绑定在该事件上的方法都不会再被执行</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>);

        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'测试一下'</str>);
    }

    <note>// 自定义的事件类</note>
    <sys>public function</sys> <act>test_event</act>(<prm>$event</prm>)
    {
        <note>// addLog为写日志的方法</note>
        TXLogger::<func>addLog</func>(<str>'触发beforeAction事件'</str>);
    }
}</pre>

        <p>另一种绑定则为一次绑定事件<code>TXEvent::one()</code>，调用参数相同，返回<code>$fd</code>操作符，当该事件被触发一次后会自动解绑</p>
        <pre><prm>$fd</prm> = TXEvent::<func>one</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>));</pre>

        <p>当然如果想要绑定多次但非长期绑定时，系统也提供了<code>bind</code>方法，参数用法类似。</p>
        <pre><note>// 第一个参数绑定方法，第二个为事件名，第三个为绑定次数，触发次数满后自动释放</note>
<prm>$fd</prm> = TXEvent::<func>bind</func>(<sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>), <str>'myEvent'</str>, <prm>$times</prm>);</pre>

        <h2 id="event-trigger">触发事件</h2>
        <p>用户可以自定义事件，同时也可以选择性的触发，可以直接使用<code>TXEvent::trigger($event, $params)</code>方法</p>
        <p>参数有两个，第一个为触发的事件名，第二个为触发传递的参数，会传递到触发方法中执行</p>
        <pre class="code"><note>// 触发myEvent事件</note>
TXEvent::<func>trigger</func>(<str>'myEvent'</str>, <sys>array</sys>(<func>get_class</func>(<prm>$this</prm>), <str>'test'</str>))

<note>// 定义事件时绑定的方法</note>
<sys>public function</sys> my_event(<prm>$event</prm>, <prm>$params</prm>)
{
    <note>// array('testService', 'test')</note>
    <sys>var_dump</sys>(<prm>$params</prm>);
}</pre>

    </div>

    <div class="bs-docs-section">
        <h1 id="forms" class="page-header">表单验证</h1>
        <p>框架提供了一套完整的表单验证解决方案，适用于绝大多数场景。</p>
        <p>表单验证支持所有类型的验证以及自定义方法</p>
        <p>简单示例：</p>
        <pre class="code">
<sys>namespace</sys> app\form;
<sys>use</sys> biny\lib\TXForm;
<note>/**
 * @property \app\service\testService $testService
 * 自定义一个表单验证类型类 继承TXForm
 */</note>
<sys>class</sys> testForm <sys>extends</sys> TXForm
{
    <note>// 定义表单参数，类型及默认值（可不写，默认null）</note>
    <sys>protected</sys> <prm>$_rules</prm> = [
        <note>// id必须为整型, 默认10</note>
        <str>'id'</str>=>[<sys>self</sys>::<prm>typeInt</prm>, 10],
        <note>// name必须非空（包括null, 空字符串）</note>
        <str>'name'</str>=>[<sys>self</sys>::<prm>typeNonEmpty</prm>],
        <note>// 自定义验证方法(valid_testCmp)</note>
        <str>'status'</str>=>[<str>'testCmp'</str>]
    ];

    <note>// 自定义验证方法</note>
    <sys>public function</sys> <act>valid_testCmp</act>()
    {
        <note>// 和Action一样可以调用Service和DAO作为私有方法</note>
        <sys>if</sys> (<prm>$this</prm>-><prm>testService</prm>-><func>checkStatus</func>(<prm>$this</prm>-><prm>status</prm>)){
            <note>// 验证通过</note>
            <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
        } <sys>else</sys> {
            <note>// 验证失败，参数可以通过getError方法获取</note>
            <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'非法类型'</str>);
        }
    }
}</pre>
        <p>定义完验证类，然后就可以在Action中使用了，可以通过<code>getForm</code>方法加载表单</p>
        <pre class="code"><note>// 加载testForm</note>
<prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>);
<note>// 验证表单字段，true/false</note>
<sys>if</sys> (!<prm>$form</prm>-><func>check</func>()){
    <note>// 获取错误信息</note>
    <prm>$error</prm> = <prm>$form</prm>-><func>getError</func>();
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'参数错误'</str>);
}
<note>// 获取对应字段</note>
<prm>$status</prm> = <prm>$form</prm>-><prm>status</prm>;
<note>// 获取全部字段 返回数组类型 ['id'=>1, 'name'=>'billge', 'status'=>2]</note>
<prm>$data</prm> = <prm>$form</prm>-><func>values</func>();
        </pre>

        <p><code>注意：</code>在<code>$_rules</code>中未定义的字段，无法在<code>$form</code>中被获取到，就算不需要验证，也最好定义一下</p>
        <p>在很多情况下，表单参数并不是都完全相同的，系统支持<code>Form复用</code>，即可以在通用的Form类中自定义一些内容</p>
        <p>比如，还是上述例子的testForm，有个类似的表单，但是多了一个字段type，而且对于status的验证方式也需要变化</p>
        <p>可以在testForm中添加一个方法</p>
        <pre class="code"><note>// 在testForm中添加</note>
<sys>public function</sys> <act>addType</act>()
{
    <note>// 添加type字段， 默认'default', 规则为非空</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'type'</str>] = [<sys>self</sys>::<prm>typeNonEmpty</prm>,<str>'default'</str>];
    <note>// 修改status的判断条件，改为valid_typeCmp()方法验证，记得要写这个方法哦</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'status'</str>][0] = <str>'typeCmp'</str>;
}</pre>

        <p>然后在Action中加载表单也需要添加<code>'addType'</code>作为参数，其他使用方法一致</p>
        <pre class="code"><prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>, <str>'addType'</str>);</pre>

        <p>一个表单验证类里可以写多个附加方法，相互直接并不会有任何影响</p>

        <h2 id="forms-type">验证类型</h2>
        <p>系统提供了7种默认验证方式，验证失败时都会记录错误信息，用户可以通过<code>getError</code>方法获取</p>
        <p><code>self::typeInt</code> 数字类型，包括整型浮点型，负数</p>
        <p><code>self::typeBool</code> 判断是否为true/false</p>
        <p><code>self::typeArray</code> 判断是否为数组类型</p>
        <p><code>self::typeObject</code> 判断是否为对象数据</p>
        <p><code>self::typeDate</code> 判断是否为一个合法的日期</p>
        <p><code>self::typeDatetime</code> 判断是否为一个合法的日期时间</p>
        <p><code>self::typeNonEmpty</code> 判断是否非空（包括null, 空字符串）</p>
        <p><code>self::typeRequired</code> 有该参数即可，可以为空字符串</p>

        <p>验证类型几乎涵盖了所有情况，如果有不能满足的类型，用户可以自定义验证方法，上述例子中已有，不再过多阐述</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="debug" class="page-header">调试</h1>
        <p>框架中有两种调试方式，一种是在页面控制台中输出的调试，方便用户对应网页调试。</p>
        <p>另一种则是和其他框架一样，在日志中调试</p>

        <h2 id="debug-console">控制台调试</h2>
        <p>Biny的一大特色既是这控制台调试方式，用户可以调试自己想要的数据，同时也不会对当前的页面结构产生影响。</p>
        <p>调试的开关在<code>/web/index.php</code>里</p>
        <pre class="code"><note>// console调试开关，关闭后控制台不会输出内容</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);</pre>
        <p>控制台调试的方式，同步异步都可以调试，但异步的调试是需要引用<code>/static/js/main.js</code>文件，这样异步ajax的请求也会把调试信息输出在控制台里了。</p>

        <p>调试方式很简单，全局可以调用<code>TXLogger::info($message, $key)</code>，另外还有warn，error，log等</p>
        <p>第一个参数为想要调试的内容，同时也支持数组，Object类的输出。第二个参数为调试key，不传默认为<code>phpLogs</code></p>
        <p><code>TXLogger::info()</code>消息 输出</p>
        <p><code>TXLogger::warn()</code>警告 输出</p>
        <p><code>TXLogger::error()</code>异常 输出</p>
        <p><code>TXLogger::log()</code>日志 输出</p>
        <p>下面是一个简单例子，和控制台的输出结果。结果会因为浏览器不一样而样式不同，效果上是一样的。</p>

        <pre class="code"><note>// 以下代码全局都可以使用</note>
TXLogger::<func>log</func>(<sys>array</sys>(<str>'cc'</str>=><str>'dd'</str>));
TXLogger::<func>error</func>(<str>'this is a error'</str>);
TXLogger::<func>info</func>(<sys>array</sys>(1,2,3,4,5));
TXLogger::<func>warn</func>(<str>"ss"</str>, <str>"warnKey"</str>);</pre>

        <p><img src="//f.wetest.qq.com/gqop/10000/20000/GuideImage_c5f68a0251b7f55efbbe0c47df9e757c.png"></p>

        <p>另外<code>TXLogger</code>调试类中还支持time，memory的输出，可以使用其对代码性能做优化。</p>
        <pre class="code"><note>// 开始结尾处加上时间 和 memory 就可以获取中间程序消耗的性能了</note>
TXLogger::<func>time</func>(<str>'start-time'</str>);
TXLogger::<func>memory</func>(<str>'start-memory'</str>);
TXLogger::<func>log</func>(<str>'do something'</str>);
TXLogger::<func>time</func>(<str>'end-time'</str>);
TXLogger::<func>memory</func>(<str>'end-memory'</str>);</pre>

        <p><img src="http://f.wetest.qq.com/gqop/10000/20000/GuideImage_c2d7aac054bd9f9cd6069445e294e826.png"></p>

        <h2 id="debug-log">日志调试</h2>

        <p>平台的日志目录在<code>/logs/</code>，请确保该目录有<code>写权限</code></p>
        <p>异常记录会生成在<code>error_{日期}.log</code>文件中，如：<code>error_2016-05-05.log</code></p>
        <p>调试记录会生成在<code>log_{日期}.log</code>文件中，如：<code>log_2016-05-05.log</code></p>

        <p>程序中可以通过调用<code>TXLogger::addLog($log, INFO)</code>方法添加日志，<code>TXLogger::addError($log, ERROR)</code>方法添加异常</p>
        <p><code>$log</code>参数支持传数组，会自动排列打印</p>
        <p><code>$LEVEL</code>可使用常量（<code>INFO</code>、<code>DEBUG</code>、<code>NOTICE</code>、<code>WARNING</code>、<code>ERROR</code>）不填即默认级别</p>
        <p>系统程序错误也都会在error日志中显示，如页面出现500时可在错误日志中查看定位</p>

    </div>

    <div class="bs-docs-section">
        <h1 id="shell" class="page-header">脚本执行</h1>
        <p>Biny框架除了提供HTTP的请求处理以外，同时还提供了一套完整的脚本执行逻辑</p>
        <p>执行入口为根目录下的<code>shell.php</code>文件，用户可以通过命令行执行<code>php shell.php {router} {param}</code>方式调用</p>
        <p>其中<code>router</code>为脚本路由，<code>param</code>为执行参数，可缺省或多个参数</p>
        <pre class="code"><note>// shell.php</note>
<note>//默认时区配置</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// 开启脚本执行（shell.php固定为true）</note>
<sys>defined</sys>(<str>'RUN_SHELL'</str>) <sys>or</sys> <sys>define</sys>(<str>'RUN_SHELL'</str>, <sys>true</sys>);
<note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
</pre>

        <h2 id="shell-router">脚本路由</h2>
        <p>路由跟http请求模式基本保持一致，分为<code>{module}/{method}</code>的形式，其中<code>{method}</code>可以缺省，默认为<code>index</code></p>
        <p>例如：<code>index/test</code>就会执行<code>indexShell</code>中的<code>action_test</code>方法，而<code>demo</code>则会执行<code>demoShell</code>中的<code>action_index</code>方法</p>
        <p>如果router缺省的话，默认会读取<code>/config/config.php</code>中的router内容作为默认路由</p>
        <pre class="code"><note>// /config/config.php</note>
<sys>return array</sys>(
    <str>'router'</str> => <sys>array</sys>(
        <note>// http 默认路由</note>
        <str>'base_action'</str> => <str>'demo'</str>,
        <note>// shell 默认路由</note>
        <str>'base_shell'</str> => <str>'index'</str>
    )
)
<note>// /app/shell/indexShell.php</note>
<sys>namespace</sys> app\shell;
<sys>use</sys> biny\lib\TXShell;
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>// 和http一样都会先执行init方法</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>//return 0 或者 不return 则程序继续执行。如果返回其他内容则输出内容后程序终止。</note>
        <sys>return</sys> 0;
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回异常，会记录日志并输出在终端</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'执行错误'</str>);
    }
}
</pre>

        <h2 id="shell-param">脚本参数</h2>
        <p>脚本执行可传复数的参数，同http请求可在方法中直接捕获，顺序跟参数顺序保持一致，可缺省</p>
        <p>另外，可以用<code>param</code>方法获取对应位置的参数</p>
        <p>例如：终端执行<code>php shell.php test/demo 1 2 aaa</code>，结果如下：</p>
        <pre class="code"><note>// php shell.php test/demo 1 2 aaa</note>
<sys>namespace</sys> app\shell;
<sys>use</sys> biny\lib\TXShell;
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>test/demo => testShell/action_demo</note>
    <sys>public function</sys> <act>action_demo</act>(<prm>$prm1</prm>, <prm>$prm2</prm>, <prm>$prm3</prm>, <prm>$prm4</prm>=<str>'default'</str>)
    {
        <note>//1, 2, aaa, default</note>
        <sys>echo</sys> <str>"<prm>$prm1</prm>, <prm>$prm2</prm>, <prm>$prm3</prm>, <prm>$prm4</prm>"</str>;
        <note>//1</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(0);
        <note>//2</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(1);
        <note>//aaa</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(2);
        <note>//default</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(3, <str>'default'</str>);
    }
}</pre>

        <p>同时框架还提供了变量化的参数传递方式，用法与http模式保持一致</p>
        <p>例如：终端执行<code>php shell.php test/demo --name="test" --id=23 demo</code>，结果如下：</p>
        <pre class="code"><note>// php shell.php test/demo --name="test" --id=23 demo</note>
<sys>namespace</sys> app\shell;
<sys>use</sys> biny\lib\TXShell;
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>test/demo => testShell/action_demo</note>
    <sys>public function</sys> <act>action_demo</act>(<prm>$id</prm>, <prm>$name</prm>=<str>'demo'</str>, <prm>$prm</prm>=<str>'default'</str>)
    {
        <note>//23, test, default</note>
        <sys>echo</sys> <str>"<prm>$id</prm>, <prm>$name</prm>, <prm>$prm</prm></prm>"</str>;
        <note>//23</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(<str>'id'</str>);
        <note>//demo</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(<str>'name'</str>);
        <note>//default</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(<str>'prm'</str>, <str>'default'</str>);

        <note>// 不带参数话模式的变量 将顺序从第0位开始</note>
        <note>// demo</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(0);
    }
}</pre>
        <p><code>注意：</code>使用变量化传递后，方法中默认参数将不会捕获非变量化的参数，如上例的<code>demo</code>需要通过<code>param</code>方法获取</p>

        <h2 id="shell-log">脚本日志</h2>
        <p>脚本执行不再具有HTTP模式的其他功能，例如<code>表单验证</code>，<code>页面渲染</code>，<code>浏览器控制台调试</code>。
            所以在<code>TXLogger</code>调试类中，<code>info/error/debug/warning</code>这几个方法将改为在终端输出</p>
        <p>同时也可以继续调用<code>TXLogger::addLog</code>和<code>TXLogger::addError</code>方法来进行写日志的操作</p>
        <p>日志目录则保存在<code>/logs/shell/</code>目录下，请确保该目录有<code>写权限</code>。格式与http模式保持一致。</p>
        <p><code>注意:</code>当程序返回<code>$this->error($msg)</code>的时候，系统会默认调用<code>TXLogger::addError($msg)</code>，请勿重复调用。</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="other" class="page-header">其他</h1>
        <p>系统有很多单例都可以直接通过<code>TXApp::$base</code>直接获取</p>
        <p><code>TXApp::$base->request</code> 为当前请求，可获取当前地址，客户端ip等</p>
        <p><code>TXApp::$base->cache</code> 为请求静态缓存，只在当前请求中有效</p>
        <p><code>TXApp::$base->session</code> 为系统session，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->memcache</code> 为系统memcache，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->redis</code> 为系统redis，可直接获取和复制，设置过期时间</p>

        <h2 id="other-request">Request</h2>
        <p>在进入<code>Controller</code>层后，<code>Request</code>就可以被调用了，以下是几个常用操作</p>
        <pre class="code"><note>// 以请求 /test/demo/?id=10 为例</note>

<note>// 获取Action名 返回test</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getModule</func>();

<note>// 获取Action对象 返回testAction</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getModule</func>(<sys>true</sys>);

<note>// 获取Method名 返回action_demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>();

<note>// 获取纯Method名 返回demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>(<sys>true</sys>);

<note>// 是否异步请求 返回false</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>isAjax</func>();

<note>// 返回当前路径  /test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>();

<note>// 返回完整路径  http://www.billge.cc/test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>(<sys>true</sys>);

<note>// 返回带参数URL  /test/demo/?id=10</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUrl</func>();

<note>// 获取来源网址 （上一个页面地址）</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getReferrer</func>();

<note>// 获取浏览器UA</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserAgent</func>();

<note>// 获取用户IP</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserIP</func>();</pre>

        <h2 id="other-cache">Cache</h2>
        <p>框架提供了<code>程序运行生命周期</code>内的全局缓存，使用非常简单</p>
        <pre class="code"><note>// 只需要赋值就可以实现cache的设置了</note>
TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testkey</prm> = <str>'test'</str>;
<note>// 获取则是直接取元素，不存在则返回null</note>
<prm>$testKey</prm> = TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testkey</prm>;</pre>

        <p>同时Cache也支持<code>isset</code>判断及<code>unset</code>操作</p>
        <pre class="code"><note>// isset 相当于先get 后isset 返回 true/false</note>
<prm>$bool</prm> = <sys>isset</sys>(TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testKey</prm>);
<note>// 删除缓存</note>
<sys>unset</sys>(TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testKey</prm>);
        </pre>

        <h2 id="other-session">Session</h2>
        <p>session的设置和获取都比较简单（与cache相同），在未调用session时，对象不会被创建，避免性能损耗。</p>
        <pre class="code"><note>// 只需要赋值就可以实现session的设置了</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm> = <str>'test'</str>;
<note>// 获取则是直接取元素，不存在则返回null</note>
<prm>$testKey</prm> = TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm>;</pre>

        <p>同时也可以通过方法<code>close()</code>来关闭session，避免session死锁的问题</p>
        <pre class="code"><note>// close之后再获取数据时会重新开启session</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>close</func>();</pre>
        <p>而<code>clear()</code>方法则会清空当前session中的内容</p>
        <pre class="code"><note>// clear之后再获取则为null</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>clear</func>();</pre>

        <p>同时session也是支持<code>isset</code>判断的</p>
        <pre class="code"><note>// isset 相当于先get 后isset 返回 true/false</note>
<prm>$bool</prm> = <sys>isset</sys>(TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testKey</prm>);</pre>

        <h2 id="other-cookie">Cookie</h2>
        <p>cookie的获取和设置都是在<code>TXApp::$base->request</code>中完成的，分别提供了<code>getCookie</code>和<code>setCookie</code>方法</p>

        <p><code>getCookie</code>参数为需要的cookie键值，如果不传，则返回全部cookie，以数组结构返回</p>
        <pre class="code"><prm>$param</prm> = TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getCookie</func>(<str>'param'</str>);</pre>
        <p><code>setCookie</code>参数有4个，分别为键值，值，过期时间(单位秒)，cookie所属路径，过期时间不传默认1天，路径默认<code>'/'</code></p>
        <pre class="code">TXApp::<prm>$base</prm>-><prm>request</prm>-><func>setCookie</func>(<str>'param'</str>, <str>'test'</str>, 86400, <str>'/'</str>);</pre>

        <h2 id="other-model">模型数据</h2>
        <p>用户可以在<code>/app/model/</code>下自定义model数据类，通过<code>TXApp::$model</code>获取，例如：</p>
        <p><code>TXApp::$model->person</code> 为当前用户，可在<code>/app/model/person.php</code>中定义</p>
        <p>除了系统预设的<code>person</code>模型外，用户也可自定义模型，例如我们新建一个<code>team</code>模型</p>
        <p>第一步，我们在<code>/app/model/</code>目录或者子目录/孙目录下新建一个文件<code>/app/model/team.php</code></p>
        <pre class="code"><note>// team.php</note>
<sys>namespace</sys> app\model;
<sys>use</sys> TXApp;
<note>/**
* @property \app\dao\teamDAO $teamDAO
* @property \app\dao\userDAO $userDAO
*/</note>
<sys>class</sys> team <sys>extends</sys> baseModel
{
    <note>/**
     * @var array 单例对象
     */</note>
    <sys>protected static</sys> <prm>$_instance</prm> = [];

    <note>/**
     * 构造函数
     * @param $id
     */</note>
    <sys>protected function</sys> <func>__construct</func>(<prm>$id</prm>)
    {
        <prm>$this</prm>-><prm>DAO</prm> = <prm>$this</prm>-><prm>teamDAO</prm>;
        <sys>if</sys> (<prm>$id</prm> !== <sys>NULL</sys>){
            <prm>$this</prm>-><prm>_data</prm> = <prm>$this</prm>-><prm>DAO</prm>-><func>getByPk</func>(<prm>$id</prm>);
            <prm>$this</prm>-><prm>_pk</prm> = <prm>$id</prm>;
        }
    }

    <note>/**
     * 自定义方法 返回用户人数
     */</note>
    <sys>public function</sys> <func>getTotal</func>()
    {
        <note>// 获取team_id标记为当前team的用户数</note>
        <sys>return</sys> <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>([<str>'team_id'</str>=><prm>$this</prm>-><prm>id</prm>])-><func>count</func>();
    }
}</pre>

        <p>然后就可以在代码中调用了，例如一个标记团队vip等级的功能，如下：</p>
        <pre class="code"><note>// 获取team数据模型</note>
<prm>$team</prm> = TXApp::<prm>$model</prm>-><func>team</func>(<prm>$id</prm>)
<sys>if</sys> (<prm>$team</prm>-><func>getTotal</func>() > 100) {
    <note>// 修改对应数据库字段并保存，以下方法为baseModel中公共方法，继承baseModel即可使用</note>
    <prm>$team</prm>-><prm>vipLevel</prm> = 1;
    <prm>$team</prm>-><func>save</func>();
}</pre>
        <p><code>注意</code>：类名，文件名，model变量名，三者需要保持一致，否者系统会找不到对应的模型。</p>

        <p>数据模型也可以定义参数的调用方式，或者多参数模式的函数调用方式，都通过<code>init</code>方法来实现</p>
        <p><code>TXApp::$model->team</code> 相当于调用 <code>\app\model\team::init()</code></p>
        <p><code>TXApp::$model->team(10, false)</code> 相当于调用 <code>\app\model\team::init(10, false)</code></p>
        <p>所以只需要覆盖掉<code>baseModel</code>中的<code>init</code>方法，即可自定义初始化模型了。</p>

        <p>另外，可以在<code>/lib/TXModel.php</code>中添加 <code>@property</code> 和  <code>@method</code> 使得IDE能够认识变量并具有补全的功能。 </p>
        <pre class="code"><note>/**
 * Class TXModel
 * @package biny\lib
 * @property \app\model\person $person
 * @method \app\model\person person($id)
 * @method \app\model\team team($id)
 */</note></pre>

        <div style="height: 200px"></div>
    </div>

</div>
<?php if (!TXApp::$base->request->isMobile()){?>
<div class="col-md-3" role="complementary">
    <nav class="bs-docs-sidebar hidden-print hidden-xs hidden-sm">
        <ul class="nav bs-docs-sidenav">

            <li>
                <a href="#overview">概览</a>
                <ul class="nav">
                    <li><a href="#overview-introduce">介绍</a></li>
                    <li><a href="#overview-files">目录结构</a></li>
                    <li><a href="#overview-level">调用关系</a></li>
                    <li><a href="#overview-index">环境配置</a></li>
                </ul>
            </li>
            <li>
                <a href="#router">路由</a>
                <ul class="nav">
                    <li><a href="#router-rule">默认路由</a></li>
                    <li><a href="#router-custom">自定义路由</a></li>
                    <li><a href="#router-ajax">异步请求</a></li>
                    <li><a href="#router-restful">Restful</a></li>
                    <li><a href="#router-param">参数获取</a></li>
                    <li><a href="#router-check">权限验证</a></li>
                </ul>
            </li>
            <li>
                <a href="#config">配置</a>
                <ul class="nav">
                    <li><a href="#config-system">系统配置</a></li>
                    <li><a href="#config-app">程序配置</a></li>
                    <li><a href="#config-env">环境配置</a></li>
                    <li><a href="#config-alias">别名使用</a></li>
                </ul>
            </li>
            <li>
                <a href="#dao">数据库使用</a>
                <ul class="nav">
                    <li><a href="#dao-connect">连接配置</a></li>
                    <li><a href="#dao-mapped">DAO映射</a></li>
                    <li><a href="#dao-simple">基础查询</a></li>
                    <li><a href="#dao-update">删改数据</a></li>
                    <li><a href="#dao-join">多联表</a></li>
                    <li><a href="#dao-filter">选择器</a></li>
                    <li><a href="#dao-extracts">复杂选择</a></li>
                    <li><a href="#dao-group">其他条件</a></li>
                    <li><a href="#dao-command">SQL模版</a></li>
                    <li><a href="#dao-cursor">游标数据</a></li>
                    <li><a href="#dao-transaction">事务处理</a></li>
                    <li><a href="#dao-cache">数据缓存</a></li>
                    <li><a href="#dao-log">语句调试</a></li>
                </ul>
            </li>
            <li>
                <a href="#view">页面渲染</a>
                <ul class="nav">
                    <li><a href="#view-param">渲染参数</a></li>
                    <li><a href="#view-tkd">自定义TKD</a></li>
                    <li><a href="#view-xss">反XSS注入</a></li>
                    <li><a href="#view-func">参数方法</a></li>
                </ul>
            </li>
            <li>
                <a href="#event">事件</a>
                <ul class="nav">
                    <li><a href="#event-init">定义事件</a></li>
                    <li><a href="#event-trigger">触发事件</a></li>
                </ul>
            </li>
            <li>
                <a href="#forms">表单验证</a>
                <ul class="nav">
                    <li><a href="#forms-type">验证类型</a></li>
                </ul>
            </li>
            <li>
                <a href="#debug">调试</a>
                <ul class="nav">
                    <li><a href="#debug-console">控制台调试</a></li>
                    <li><a href="#debug-log">日志调试</a></li>
                </ul>
            </li>
            <li>
                <a href="#shell">脚本执行</a>
                <ul class="nav">
                    <li><a href="#shell-router">脚本路由</a></li>
                    <li><a href="#shell-param">脚本参数</a></li>
                    <li><a href="#shell-log">脚本日志</a></li>
                </ul>
            </li>
            <li>
                <a href="#other">其他</a>
                <ul class="nav">
                    <li><a href="#other-request">Request</a></li>
                    <li><a href="#other-cache">Cache</a></li>
                    <li><a href="#other-session">Session</a></li>
                    <li><a href="#other-cookie">Cookie</a></li>
                    <li><a href="#other-model">模型数据</a></li>
                </ul>
            </li>

        </ul>
        <a class="back-to-top" href="#top">
            返回顶部
        </a>

    </nav>
</div>
<?php } ?>

</div>
</div>

<?php include TXApp::$view_root . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$webRoot?>/static/js/demo.js"></script>