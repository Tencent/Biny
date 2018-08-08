<?php include TXApp::$view_root . "/base/common.tpl.php" ?>
<?php include TXApp::$view_root . "/base/header.tpl.php" ?>
<link href="<?=$webRoot?>/static/css/demo.css" rel="stylesheet" type="text/css"/>
<style type="text/css">.bs-docs-section > p{word-break: normal}</style>
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
        <h1 id="overview" class="page-header">Overview</h1>
        <p>Biny is a high performance lightweight PHP framework. </p>
        <p>It follows the MVC pattern for rapid development of modern Web applications </p>
        <p>Biny code is simple and elegant. The application layer, data layer, and template rendering layer of the package is simple and easy to understand. This makes it quick to pick up. </p>
        <p>Biny is high performance. Framework comes default with response time of less than 1ms. Stand-alone QPS easily up to 3000.</p>

        <h2 id="overview-introduce">Introduce</h2>
        <p>Support cross library join table, conditional compound filter, query PK cache, etc. </p>
        <p>Synchronous asynchronous request separation, automatic loading management of classes </p>
        <p>Supports Form validation and supports event triggering mechanisms </p>
        <p>Supports browser side debugging, rapid positioning problems and performance bottlenecks </p>
        <p>With SQL anti injection, HTML automatic anti XSS and other characteristics </p>
        <p>Frameword Wiki：<a href="http://www.billge.cc">http://www.billge.cc</a></p>
        <p>GitHub url：<a href="https://github.com/Tencent/Biny">https://github.com/Tencent/Biny</a></p>

        <h2 id="overview-files">Directory</h2>
        <div class="col-lg-3"><img src="http://f.wetest.qq.com/gqop/10000/20000/GuideImage_cb2a0980064cb1e61242742ed0b183be.png"></div>
        <div class="col-lg-8" style="margin-left: 20px">
            <p><code>/app/</code> Top directory</p>
            <p><code>/app/config/</code> App config layer</p>
            <p><code>/app/controller/</code> Controller Action layer</p>
            <p><code>/app/dao/</code> Database table layer</p>
            <p><code>/app/event/</code> Event layer</p>
            <p><code>/app/form/</code> Form layer</p>
            <p><code>/app/model/</code> Model layer</p>
            <p><code>/app/service/</code> Service layer</p>
            <p><code>/app/template/</code> View Template layer</p>
            <p><code>/config/</code> Lib config layer</p>
            <p><code>/lib/</code> System lib layer</p>
            <p><code>/extends/</code> Custom lib layer</p>
            <p><code>/logs/</code> Log direcory</p>
            <p><code>/web/</code> Executing entry directory</p>
            <p><code>/web/static/</code> Static resource directory</p>
            <p><code>/web/index.php</code> Executing entry file</p>
            <p><code>/shell.php</code> Shell model start file</p>
        </div>
        <div style="clear: both"></div>

        <h2 id="overview-level">Call relation</h2>
        <p><code>Action</code> is the general routing entry, and <code>Action</code> can call the private object <code>Service</code> business layer and the <code>DAO</code> database layer</p>
        <p><code>Service</code> business layer can call private object <code>DAO</code> database layer</p>
        <p>The program can call the system method under the Lib library, such as <code>TXLogger</code> (debug component)</p>
        <p><code>TXApp::$base</code>is a global singleton class, which can be called globally</p>
        <p><code>TXApp::$base->request</code> is the current request, access to the current address, client IP, etc.</p>
        <p><code>TXApp::$base->session</code> is the system session, can be directly obtained and copied, set the expiration time</p>
        <p><code>TXApp::$base->memcache</code> is the system Memcache, can be directly obtained and copied, set the expiration time</p>
        <p><code>TXApp::$base->redis</code>  is the system redis, can be directly obtained and copied, set the expiration time</p>

        <p>Users can customize the model data class under <code>/app/model/</code>, and get them through <code>TXApp::$model</code>, for example:</p>
        <p><code>TXApp::$model->person</code> is the current user,It can be defined in <code>/app/model/person.php</code></p>

        <p>Simple example</p>
        <pre class="code"><sys>namespace</sys> app\controller;
<sys>use</sys> TXApp;
<span class="nc">/**
* main Action
* @property \app\service\projectService $projectService
* @property \app\dao\projectDAO $projectDAO
*/  </span>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>// The init method is executed before the action is executed</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>// Login page adjustment when not logged in</note>
        <sys>if</sys>(!TXApp::<prm>$model</prm>-><prm>person</prm>-><func>exist</func>()){
            <sys>return</sys> TXApp::<prm>$base</prm>-><prm>request</prm>-><func>redirect</func>(<str>'/auth/login/'</str>);
        }
    }

    <note>// Default routing index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//  Get current user</note>
        <prm>$person</prm> = TXApp::<prm>$model</prm>-><prm>person</prm>;
        <prm>$members</prm> = TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>get</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>);
        <sys>if</sys> (!<prm>$members</prm>){
            <note>// Get the members of the user's project</note>
            <prm>$project</prm> = <prm>$this</prm>-><prm>projectDAO</prm>-><func>find</func>(<sys>array</sys>(<str>'id'</str>=><prm>$person</prm>-><prm>project_id</prm>));
            <prm>$members</prm> = <prm>$this</prm>-><prm>projectService</prm>-><func>getMembers</func>(<prm>$project</prm>[<str>'id'</str>]);
            TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>set</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>, <prm>$members</prm>);
        }
        <note>// return project/members.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'project/members'</str>, <sys>array</sys>(<str>'members'</str>=><prm>$members</prm>));
    }
}</pre>
        <p>P.S: The usage of the example will be described in detail below</p>

        <h2 id="overview-index">Environmental allocation</h2>
        <p>The PHP version must be more than <code>5.5</code>, including <code>5.5</code></p>
        <p>If you need to use the database, you need to install and enable the <code>mysqli expansion</code></p>
        <p>In <code>php.ini</code> you needed to set the <code>short_open_tag On</code></p>
        <p><code>/config/autoload.php</code> is the automatic loading file, must have <code>write permissions</code></p>
        <p><code>/logs/</code> is the log folder, also must have <code>write permissions</code></p>
        <p>This example describes the Linux nginx configuration</p>
        <p>Root needs to point to the <code>/web/</code> directory, for example:</p>
        <pre class="code"><sys>location</sys> / {
    <const>root</const>   /data/billge/biny/web/; <note>// Here is the absolute path of the framework /web directory</note>
    <act>index</act>  index.php index.html index.htm;
    <act>try_files</act> $uri $uri/ /index.php?$args;
}</pre>
        <p>The configuration of Apache is as follows:：</p>
<pre class="code"><note># Set the document root to the /web directory</note>
<const>DocumentRoot</const> <str>"/data/billge/biny/web/"</str>

&lt;<const>Directory</const> <str>"/data/billge/biny/web/"</str>>
    <act>RewriteEngine</act> <sys>on</sys>
    <note># If the request is the existence of a file or directory, direct access</note>
    <act>RewriteCond</act> %{REQUEST_FILENAME} !-f
    <act>RewriteCond</act> %{REQUEST_FILENAME} !-d
    <note># If the request is not the real file or directory, distribute requests to index.php</note>
    <act>RewriteRule</act> . index.php

    <note># ...other settings...  </note>
&lt;/<const>Directory</const>> </pre>
        <p><code>/web/index.php</code> Is the main entrance program, which has several key configuration</p>
        <pre class="code"><note>// Default timezone configuration</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// Open debug mode (output exception)</note>
<sys>defined</sys>(<str>'SYS_DEBUG'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_DEBUG'</str>, <sys>true</sys>);
<note>// Open Logger debugging in browser console</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);
<note>// dev pre pub environment</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
<note>// System maintenance</note>
<sys>defined</sys>(<str>'isMaintenance'</str>) <sys>or</sys> <sys>define</sys>(<str>'isMaintenance'</str>, <sys>false</sys>);</pre>

        <p>The <code>SYS_ENV</code> values in the environment also has bool, convenient use of judgment</p>
        <pre class="code"><note>// \lib\TXApp.php </note>
<note>// Devnet</note>
<sys>defined</sys>(<str>'ENV_DEV'</str>) <sys>or define</sys>(<str>'ENV_DEV'</str>, <const>SYS_ENV</const> === 'dev');
<note>// Pre release</note>
<sys>defined</sys>(<str>'ENV_PRE'</str>) <sys>or define</sys>(<str>'ENV_PRE'</str>, <const>SYS_ENV</const> === 'pre');
<note>// Release</note>
<sys>defined</sys>(<str>'ENV_PUB'</str>) <sys>or define</sys>(<str>'ENV_PUB'</str>, <const>SYS_ENV</const> === 'pub');</pre>
    </div>

    <div class="bs-docs-section">
        <h1 id="router">Route</h1>
        <p>The basic architecture of MVC routing model, corresponding to the first layer <code>action</code>, second layer corresponding to <code>method</code> (default <code>index</code>)</p>
        <h2 id="router-rule">Default rule</h2>
        <p>In the <code>/app/controller</code> directory, the file can be placed in any directory or directory in the sun. But must ensure that the file name is consistent with the class name, and not repeat</p>
        <p>example：/app/controller/Main/testAction.php</p>
        <pre class="code"><note>// http://www.billge.cc/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//default route index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//return test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/test'</str>);
    }
}</pre>
        <p>At the same time also can configure multiple sub routing in the same file</p>
        <pre class="code"><note>//sub routing find method action_{$router}</note>
<note>// http://www.billge.cc/test/demo1</note>
<sys>public function</sys> <act>action_demo1</act>()
{
    <note>//return test/demo1.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo1'</str>);
}

<note>// http://www.billge.cc/test/demo2</note>
<sys>public function</sys> <act>action_demo2</act>()
{
    <note>//return test/demo2.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo2'</str>);
}</pre>

        <h2 id="router-custom">Custom route</h2>
        <p>In addition to the default routing, the routing rules can be customized and configured in <code>/config/config.php</code></p>
        <p>The custom routing rules are executed first, then the default rules are taken after the matching fails, and the strings after the parameter colons are automatically converted into <code>regular matcher</code></p>
<pre class="code"><note>/config/config.php</note>
<str>'routeRule'</str> => <sys>array</sys>(
    <note>// test/(\d+).html will be automatically forwarded to the action_view method in testAction
    </note>
    <str>'<prm>test</prm>/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/view'</str>,
    <note>// The matching parameters can be used in dynamic routing forwarding</note>
    <str>'<prm>test</prm>/&lt;<prm>method</prm>:[\w_]+&gt;/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/&lt;<prm>method</prm>&gt;'</str>,
),

<note>/app/controller/testAction.php</note>
<note>// test/272.html Regular match content is introduced into the method</note>
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


        <h2 id="router-ajax">Ajax request</h2>
        <p>The asynchronous request contains POST, Ajax and many other requests, and the system automatically performs <code>CSRF</code> and processing</p>
        <p>The response method and the synchronization request are consistent in the program, and the return <code>$this->error()</code> will automatically differentiate from the synchronous request and return the <code>JSON data</code></p>
        <pre class="code"><note>// http://www.billge.cc/test/demo3</note>
<sys>public function</sys> <act>action_demo3</act>()
{
    <prm>$ret</prm> = <sys>array</sys>(<str>'result'</str>=>1);
    <note>//return json {"flag": true, "ret": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>correct</func>(<prm>$ret</prm>);

    <note>//return json {"flag": false, "error": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<prm>$ret</prm>);
}</pre>
        <p>The framework provides a full set of <code>CSRF authentication</code> mechanisms that are <code>opened</code> by default and can be closed by <code>$csrfValidate = false</code> in Action.</p>
        <pre class="code"><note>// http://www.billge.cc/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>// close CSRF authentication</note>
    <sys>protected</sys> <prm>$csrfValidate</prm> = <sys>false</sys>;

    <note>// default route index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//return test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    }
}</pre>

        <p>When the CSRF validation is opened, the front-end Ajax request needs to preload reference <code>/static/js/main.js</code> file, and when the Ajax commits, the system adds the validation field automatically.</p>
        <p>The POST request will also trigger CSRF verification, need to add the following data fields in form:</p>
        <pre class="code"><note>// add in form</note>
<act>&lt;input</act> type="<str>text</str>" name="<str>_csrf</str>" hidden value="<sys>&lt;?=</sys><prm>$this</prm>-><func>getCsrfToken</func>()<sys>?&gt;</sys>"<act>/></act></pre>

        <p>You can also get it in JS (the premise is to refer to the <code>/static/js/main.js</code> file) and add it to the POST parameter.</p>
        <pre class="code"><sys>var</sys> <prm>_csrf</prm> = <func>getCookie</func>(<str>'csrf-token'</str>);</pre>


        <h2 id="router-restful">Restful</h2>
        <p>Biny also supports the request of the restful protocol, and <code>$restApi</code> can be set to <code>true</code> in the Action class, and the Action will parse the routing with the protocol of restful</p>
        <pre class="code"><sys>namespace</sys> app\controller;
<note>/**
 * restful demo
 * @property \app\dao\userDAO $userDAO
 */</note>
<sys>class</sys> restAction <sys>extends</sys> baseAction
{
    <note>// The action analyzes routing with the restful protocol</note>
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

        <p>Similarly, the restful protocol can also be configured through custom routing mode, for example:</p>
        <pre class="code"><note>/config/config.php</note>
<str>'routeRule'</str> => <sys>array</sys>(
    <note>// rest/(\d+) routing will be automatically forwarded to the {method}_test method in restAction</note>
    <str>'<prm>rest</prm>/&lt;<prm>id</prm>:\d+&gt;'</str> => <str>'rest/test'</str>,
    <note>// The matching parameters can be used in dynamic routing forwarding</note>
    <str>'<prm>v</prm>&lt;<prm>version</prm>:\d+&gt;/rest/&lt;<prm>id</prm>:\d+&gt;/&lt;<prm>method</prm>:[\w_]+&gt;'</str> => <str>'rest/&lt;<prm>method</prm>&gt;'</str>,
),

<note>/app/controller/restAction.php</note>
<note>// [DELETE] http://www.billge.cc/v2/rest/123/person</note>
<sys>public function</sys> <act>DELETE_person</act>(<prm>$version</prm>, <prm>$id</prm>)
{
    <sys>echo</sys> <prm>$version</prm>; <note>// 2</note>
    <sys>echo</sys> <prm>$id</prm>; <note>// 123</note>
}
<note>// [PUT] http://www.billge.cc/rest/272 Regular match content is introduced into the method</note>
<sys>public function</sys> <act>PUT_test</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 272</note>
}
</pre>

        <h2 id="router-param">Get Param</h2>
        <p>The method can directly receive GET parameters, and can be assigned a default value, empty returns null</p>
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

        <p>At the same time, you can also call <code>param</code>, <code>get</code>, <code>post</code> method to obtain the parameters.</p>
        <p><code>param($key, $default)</code> Get the GET/POST/JSON parameter of {$key}, the default value is {$default}</p>
        <p><code>get($key, $default)</code> Get the GET parameter of {$key}, the default value is {$default}</p>
        <p><code>post($key, $default)</code> Get the POST parameter of {$key}, the default value is {$default}</p>
        <p><code>getJson($key, $default)</code> Get the JSON data with {$key} by row input json flow, the default value is {$default}</p>
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

        <h2 id="router-check">Authorization verification</h2>
        <p>The framework provides a complete set of permissions validation logic for authorization of routing all <code>method</code></p>
        <p>You need to add the <code>privilege</code> method in action, the specific field return as follows</p>
        <pre class="code"><sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>private</sys> <prm>$key</prm> = <str>'test'</str>;

    <sys>protected function</sys> <act>privilege</act>()
    {
        <sys>return array</sys>(
            <note>// login validation（define in privilegeService）</note>
            <str>'login_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => <str>'*'</str>, <note>// bind actions, * for all methods</note>
                <str>'params'</str> => [],   <note>// params (can access $this) not have to send</note>
                <str>'callBack'</str> => [], <note>// callback when failed, not have to send</note>
            ),
            <str>'my_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => [<str>'index'</str>], <note>// bind action_index</note>
                <str>'params'</str> => [<prm>$this</prm>-><prm>key</prm>],   <note>// send $this->key</note>
                <str>'callBack'</str> => [<prm>$this</prm>, <str>'test'</str>], <note>// call $this->test() when failed</note>
            ),
        );
    }
    <note>// After login_required and my_required are verified success, the method will be called</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// do something</note>
    }
    <note>// called after failed of my_required validation, param $action is the current action object</note>
    <sys>public function</sys> <act>test</act>(<prm>$action</prm>, <prm>$error</prm>)
    {
        <note>// do something</note>
    }
}</pre>

        <p>Then define the validation method in <code>privilegeService</code></p>
        <pre class="code"><note>The first parameter $action is testAction, $key is the params argument</note>
<sys>public function</sys> <act>my_required</act>(<prm>$action</prm>, <prm>$key</prm>=<sys>NULL</sys>)
{
    <sys>if</sys>(<prm>$key</prm>){
        <note>// pass</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    } <sys>else</sys> {
        <note>// fail, error message can get by $this->privilegeService->getError()</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'key not exist'</str>);
    }
}</pre>

        <p><code>callBack</code> is the method called when the check fails. then throw out the error exception, and the program will not continue to execute.</p>

        <p>If different routes need the same authentication method, and need send the different parameters. The <code>requires</code> parameter can be used, and the example is used for reference:</p>
<pre class="code"><sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>protected function</sys> <act>privilege</act>()
    {
        <sys>return array</sys>(
            <str>'privilege_required'</str> => <sys>array</sys>(
                <note>// The corresponding operation permissions are introduced according to different routes</note>
                <str>'requires'</str> => [
                    [<str>'actions'</str>=>[<str>'index'</str>, <str>'view'</str>], <str>'params'</str>=>[TXPrivilege::<prm>user</prm>]],
                    [<str>'actions'</str>=>[<str>'edit'</str>, <str>'delete'</str>], <str>'params'</str>=>[TXPrivilege::<prm>admin</prm>]],
                ],
                <str>'callBack'</str> => [<prm>$this</prm>, <str>'test'</str>], <note>// called $this->test() when check failed</note>
            ),
        );
    }

<note>// privilegeService</note>
<sys>public function</sys> <act>privilege_required</act>(<prm>$action</prm>, <prm>$privilege</prm>)
{
    <sys>if</sys>(TXApp::<prm>$model</prm>-><prm>person</prm>-><func>hasPrivilege</func>(<prm>$privilege</prm>)){
        <note>// the user has the privilege</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    } <sys>else</sys> {
        <note>// check failed, send the error message</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'forbidden'</str>);
    }
}</pre>

<p><code>Note</code>: when you use the <code>requires</code> parameter <code>actions</code> and <code>params</code> parameter will be covered</p>

    </div>

    <div class="bs-docs-section">
        <h1 id="config" class="page-header">Config</h1>
        <p>The configuration is divided into two blocks, one is the system configuration, one is the application configuration</p>
        <p><code>/config/</code> System configuration path</p>
        <p><code>/app/config/</code> Application configuration path</p>

        <h2 id="config-system">System Config</h2>
        <p><code>/config/config.php</code> System common configuration (Including the default routing, custom routing configuration etc.)</p>
        <pre class="code"><sys>return array</sys>(
    <note>// route configuration</note>
    <str>'router'</str> => <sys>array</sys>(
        <str>'base_action'</str> => <str>'demo'</str>, <note>// default action entry</note>
        <str>'base_shell'</str> => <str>'index'</str>, <note>// default shell entry</note>

        <note>// Static configuration</note>
        <str>'routeRule'</str> => <sys>array</sys>(
            <note>// test/123 => test/view</note>
            <str>'test/&lt;id:[\w_]+>'</str> => <str>'test/view'</str>,
            <note>// abc/test/123 => test/abc</note>
            <str>'&lt;method:\w+>/test/&lt;id:\d+>.html'</str> => <str>'test/&lt;method>'</str>,
        ),
    ),

    <note>// autoload configuration</note>
    <str>'autoload'</str> => <sys>array</sys>(
        <str>'autoPath'</str> => <str>'config/autoload.php'</str>,
        <note>// refresh autoload skip seconds</note>
        <str>'autoSkipLoad'</str> => 5,
        <str>'autoThrow'</str> => <sys>true</sys>, <note>// when use of an external autoload mechanism (such as composer) should be set to false</note>
    ),

    <note>// request configuration</note>
    <str>'request'</str> => <sys>array</sys>(
        <str>'trueToken'</str> => <str>'biny-csrf'</str>,
        <str>'csrfToken'</str> => <str>'csrf-token'</str>,
        <str>'csrfPost'</str> => <str>'_csrf'</str>,
        <str>'csrfHeader'</str> => <str>'X-CSRF-TOKEN'</str>,

        <note>// get userip cookie key</note>
        <str>'userIP'</str> => <str>''</str>,
        <note>// return tpl when use this cookie in ajax</note>
        <str>'showTpl'</str> => <str>'X_SHOW_TEMPLATE'</str>,
        <note>//csrf white list</note>
        <str>'csrfWhiteIps'</str> => <sys>array</sys>(
            <str>'127.0.0.1/24'</str>
        ),
        <note>// control language cookie</note>
        <str>'languageCookie'</str> => <str>'biny_language'</str>
    ),

    <note>// response configuration</note>
    <str>'response'</str> => <sys>array</sys>(
        <str>'jsonContentType'</str> => <str>'application/json'</str>,
        <note>// Compatible with old versions</note>
        <str>'paramsType'</str> => <str>'one'</str>,  <note>// one or keys</note>
        <note>// param name in tpl</note>
        <str>'paramsKey'</str> => <str>'PRM'</str>,
        <str>'objectEncode'</str> => <sys>true</sys>, <note>// escaped object param</note>
    ),

    <note>// log configuration</note>
    <str>'logger'</str> => <sys>array</sys>(
        <note>// enabled write file</note>
        <str>'files'</str> => <sys>true</sys>,
        <note>// custom message log
//        'sendLog' => array('TXCommon', 'sendLog'),
        // custom error log callback function
//        'sendError' => array('TXCommon', 'sendError'),
        // error level, will trigger above NOTICE</note>
        <str>'errorLevel'</str> => <const>NOTICE</const>,
        <note>// SQL slow query threshold(ms)</note>
        <str>'slowQuery'</str> => 1000,
    ),

    <note>// database configuration</note>
    <str>'database'</str> => <sys>array</sys>(
        <str>'returnIntOrFloat'</str> => <sys>true</sys>, <note>// return int or float in select command</note>
        <str>'returnAffectedRows'</str> => <sys>false</sys>, <note>// return affected rows in update/delete command, -1 means failed</note>
    ),

    <note>// cache configuration</note>
    <str>'cache'</str> => <sys>array</sys>(
        <str>'pkCache'</str> => <str>'tb:%s'</str>,
        <str>'session'</str> => <sys>array</sys>(
            <str>'save_handler'</str>=><str>'files'</str>,  <note>//redis memcache</note>
            <str>'maxlifetime'</str> => 86400    <note>// expire seconds</note>
        ),
        <note>// enabled redis serialize</note>
        <str>'serialize'</str> => <sys>true</sys>,
    ),

    <note>//exception configuration</note>
    <str>'exception'</str> => <sys>array</sys>(
        <note>// return tpl when error</note>
        <str>'exceptionTpl'</str> => <str>'error/exception'</str>,
        <str>'errorTpl'</str> => <str>'error/msg'</str>,

        <str>'messages'</str> => <sys>array</sys>(
            500 => <str>'Some Error in page, please try latter'</str>,
            404 => <str>'Page not found'</str>,
            403 => <str>'Request forbidden, please connect web admin'</str>
        )
    ),



)</pre>
        <p><code>/config/autoload.php</code> The configuration of the system automatic loading class will be generated automatically according to user code, without configuration, but must have <code>write permissions</code></p>
        <p><code>/config/exception.php</code> System Exception configuration</p>
        <p><code>/config/http.php</code> HTTP code</p>
        <p><code>/config/database.php</code> DAO mapping configuration</p>

        <p>User can get by the method <code>TXApp:: $base->config->get</code></p>
        <p>for example：</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'session_name'</str> => <str>'biny_sessionid'</str>
}

<note>// The second parameter is the file name (default is config), the third parameter is whether to use alias (default is true)</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'session_name'</str>, <str>'config'</str>, <sys>true</sys>);</pre>

        <h2 id="config-app">App config</h2>
        <p>Application configuration directory is <code>/app/config/</code></p>
        <p>Default has <code>dns.php</code>(connect config) 和 <code>config.php</code>(default path config)</p>
        <p>The mode of use is basically consistent with the system configuration</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'memcache'</str> => <sys>array</sys>(
        <str>'host'</str> => <str>'10.1.163.35'</str>,
        <str>'port'</str> => 12121
    )
}

<note>// The second parameter is the file name (default is config), the third parameter is whether to use alias (default is true)</note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'memcache'</str>, <str>'dns'</str>);</pre>

        <h2 id="config-env">Environmental allocation</h2>
        <p>The system of different environment configuration can be distinguished</p>
        <p>Environment can configure in <code>/web/index.php</code></p>
        <pre class="code"><note>// dev pre pub </note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);</pre>

        <p>When you use <code>TXApp::$base->config->get</code>, system will automatically find the corresponding configuration file</p>
        <pre class="code"><note>// when 'dev' will find file /config/config_dev.php</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'test'</str>, <str>'config'</str>);

<note>// when 'pub' will find file  /config/dns_pub.php文件</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'test2'</str>, <str>'dns'</str>);</pre>

        <p>Public configuration files can be placed in files that do not add environment names like <code>/config/config.php</code></p>
        <p>When coexist <code>config.php</code> and <code>config_dev.php</code>, The contents of the file with the environment configuration will cover the general configuration</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns'</str>,
    <str>'demo'</str> => <str>'dns'</str>,
}

<note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns_dev</str>
}

<note>// return 'dns_dev' </note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'test'</str>, <str>'dns'</str>);

<note>// return 'dns' </note>
TXApp::<prm>$base</prm>-><prm>app_config</prm>-><func>get</func>(<str>'demo'</str>, <str>'dns'</str>);</pre>
        <p>System configuration and Application configuration are used in the same way</p>

        <h2 id="config-alias">Alias</h2>
        <p>Support alias configuration used, can be in the alias on both sides with <code>@</code></p>
        <p>The system has a default alias <code>web</code> will replace the current path</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/'</str>
}

<note>// return '/biny/my-path/' </note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>);</pre>

        <p>Users can also customize define alias, for example:</p>
        <pre class="code"><note>// before method config->get </note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>setAlias</func>(<str>'time'</str>, <sys>time</sys>());

<note>// config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/?time=@time@'</str>
}

<note>// return '/biny/my-path/?time=1461141347'</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>);

<note>// return '@web@/my-path/?time=@time@'</note>
TXApp::<prm>$base</prm>-><prm>config</prm>-><func>get</func>(<str>'path'</str>, <str>'config'</str>, <sys>false</sys>);</pre>

        <p>Of course, if you want to avoid alias escape, you can send <code>false</code> as third parameters in method <code>TXApp::$base->config->get</code>.</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="dao" class="page-header">Database</h1>
        <p>Biny requires that each database table need to build a separate class and put it under the <code>/dao</code> directory.
            Like other directories, support multi tier file structures, you can place file in subdirectories or sun directories, but class names must be <code>unique</code>.</p>
        <p>All parameters imported into the DAO method are <code>automatically escaped</code>, and the risk of <code>SQL injection</code> can be completely avoided</p>
        <p>for example：</p>
        <pre class="code"><note>// testDAO.php    Match the class name</note>
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <note>// Database name, Array mains master-slave separation：['database', 'slaveDb'] match the config in dns.php. default is 'database'</note>
    <sys>protected</sys> <prm>$dbConfig</prm> = <str>'database'</str>;
    <note>// table name</note>
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// primary key, array mains double key：['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// enable cacke used, default false</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;

    <note>// Splitting Table method, default method is add id instead</note>
    <sys>public function</sys> <act>choose</act>(<prm>$id</prm>)
    {
        <prm>$sub</prm> = <prm>$id</prm> <sys>%</sys> 100;
        <prm>$this</prm>-><func>setDbTable</func>(<sys>sprintf</sys>(<str>'%s_%02d'</str>, <prm>$this</prm>-><prm>table</prm>, <prm>$sub</prm>));
        <sys>return</sys> <prm>$this</prm>;
    }
}</pre>


        <h2 id="dao-connect">Connect</h2>
        <p>Database information configuration in <code>/app/config/dns.php</code>, also can configuration with environment in <code>dns_dev.php</code>/<code>dns_pre.php</code>/<code>dns_pub.php</code></p>
        <p>Common parameters include：</p>
        <pre class="code"><note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'database'</str> => <sys>array</sys>(
        <note>// IP</note>
        <str>'host'</str> => <str>'127.0.0.1'</str>,
        <note>// Database name</note>
        <str>'database'</str> => <str>'Biny'</str>,
        <note>// User name</note>
        <str>'user'</str> => <str>'root'</str>,
        <note>// Password</note>
        <str>'password'</str> => <str>'pwd'</str>,
        <note>// Code format</note>
        <str>'encode'</str> => <str>'utf8'</str>,
        <note>// Port</note>
        <str>'port'</str> => 3306,
        <note>// Enable keep alive (default false)</note>
        <str>'keep-alive'</str> => true,
    )
)</pre>
        <p>You can also configure multiple, and you need configuration parameter '$dbConfig' in the DAO class (default is <code>'database'</code>)</p>


        <h2 id="dao-mapped">DAO mapping</h2>
        <p>The appeal DAO needs to write the PHP file, and the frame here also provides a simple version of the mapping</p>
        <p>User can configuration in <code>/config/database.php</code>, for example:</p>
        <pre class="code"><note>// database.php</note>
<sys>return array</sys>(
    <str>'dbConfig'</str> => array(
        <note>// Equivalent to creating the testDAO.php</note>
        <str>'test'</str> => <str>'Biny_Test'</str>
    )
);</pre>
        <p>Then you can use <code>testDAO</code> in <code>Action、Service、Model</code></p>

<pre class="code"><note>// testAction.php
<sys>namespace</sys> app\controller;
/**
* use property to make IDE know the $testDAO meaning
* @property \biny\lib\TXSingleDAO $testDAO
*/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// The testDAO here is generated by the mapping without the cache operation in baseDAO
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
    }
}</pre>
        <p><code>Note</code>: The mapped DAO does not have the function of setting database (master/slave database point to <code>'database'</code>)</p>
        <p>And can not use function with PK cache (<code>getByPK、updateByPK、deleteByPK</code> etc)</p>
        <p>If you need use function with PK cache, you should create dao file in <code>/dao</code> directory and configurate parameters you need</p>

        <h2 id="dao-simple">Simple query</h2>
        <p>DAO provided common query function like <code>query</code>, <code>find</code>, and pretty simple to use</p>
        <pre class="code"><note>// testAction.php
<sys>namespace</sys> app\controller;
/**
 * use property to make IDE know the $testDAO meaning
 * @property \app\dao\testDAO $testDAO
 */</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// return array with all data in testDAO, The format is two-dimensional array
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
        <note>// first parameter is key you need to return [['id'=>1, 'name'=>'xx'], ['id'=>2, 'name'=>'yy']]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>));
        <note>// the second parameter is the dictionary key, will duplicate removal [1 => ['id'=>1, 'name'=>'xx'], 2 => ['id'=>2, 'name'=>'yy']]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>), <str>'id'</str>);

        <note>// return first data, the format is array ['id'=>1, 'name'=>'xx', 'type'=>2]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>();
        <note>// parameter is key you need to return ['name'=>'xx']</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>('name');
    }
}</pre>
        <p>Biny also support <code>count</code>, <code>max</code>, <code>sum</code>, <code>min</code>, <code>avg</code> etc Basic operations,
            count with parameter will return the count by <code>duplicate removal</code></p>
        <pre class="code"><note>// count(*) </note>
<prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>();
<note>// count(distinct `name`) duplicate removal</note>
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
        <p>All operations here are simple operations, if you need complex methods or multiple table operations, you should use method <code>addition</code></p>


        <h2 id="dao-update">Delete/Update</h2>
        <p>You can use method like <code>update</code>, <code>delete</code>, <code>add</code> etc</p>
        <p>Method <code>update</code> to update data, return success (<code>true</code>) for failure(<code>false</code>), condition can reference chapter <code>seletor</code> after</p>
<pre class="code"><note>// update `DATABASE`.`TABLE` set `name`='xxx', `type`=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'xxx'</str>, <str>'type'</str>=>5));</pre>

        <p>Method <code>delete</code> to delete the data, return success (<code>true</code>) for failure(<code>false</code>), condition can reference chapter <code>seletor</code> after</p>
<pre class="code"><note>// delete from `DATABASE`.`TABLE`</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>delete</func>();</pre>

        <p>Mehtod <code>add</code> to add the data, when insert success will return the increasing ID, when second parameter false will return success (<code>true</code>) for failure(<code>false</code>)</p>
<pre class="code"><note>// insert into `DATABASE`.`TABLE` (`name`,`type`) values('test', 1)</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<note>// return true/false</note>
<prm>$id</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>add</func>(<prm>$sets</prm>, <sys>false</sys>);</pre>

        <p>Biny also can return affected_rows, you can configuration in <code>/config/config.php</code>, and replace parameter <code>returnAffectedRows</code> to <code>true</code></p>

        <p>Method <code>update</code> also can use in common operations, for example:</p>

        <pre class="code"><note>// update `DATABASE`.`TABLE` set `type`=`type`+5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>([<str>'type'</str>=>[<str>'+'</str>=>5]]);
<note>// update `DATABASE`.`TABLE` set `type`=`count`-`num`-4</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>([<str>'type'</str>=>[<str>'-'</str>=>[<str>'count'</str>, <str>'num'</str>, 4]]]);
        </pre>

        <p>Method <code>createOrUpdate</code> to add data and update when duplication</p>
<pre class="code"><note>// The first parameter is the insert array, the second parameter is the update parameter when the failure occurs, and update the first parameter when not pass the second</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>createOrUpdate</func>(<prm>$sets</prm>);</pre>

        <p>Method <code>addList</code> can batch adding data, return success (<code>true</code>) for failure(<code>false</code>)</p>
<pre class="code"><note>// parameter is two-dimensional array</note>
<prm>$sets</prm> = <sys>array</sys>(
    <sys>array</sys>(<str>'name'</str>=><str>'test1'</str>, <str>'type'</str>=>1),
    <sys>array</sys>(<str>'name'</str>=><str>'test2'</str>, <str>'type'</str>=>2),
);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addList</func>(<prm>$sets</prm>);</pre>

        <h2 id="dao-join">Join Table</h2>
        <p>The framework supports the multi join table model, and the DAO classes have <code>join</code>, <code>leftJoin</code>, and <code>rightJoin</code> methods.</p>
        <p>The parameter is the connection relation</p>
        <pre class="code"><note>// on `user`.`projectId` = `project`.`id` and `user`.`type` = `project`.`type`</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>, <str>'type'</str>=><str>'type'</str>));</pre>

        <p><code>$DAO</code> can continue to join, connect third tables, the connection is two-dimensional array, the first array corresponds to the first table and the new table relationship, the second array corresponds to second tables and the new table relationship</p>
        <pre class="code"><note>// on `user`.`testId` = `test`.`id` and `project`.`type` = `test`.`status`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>leftJoin</func>(<prm>$this</prm>-><prm>testDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(<str>'testId'</str>=><str>'id'</str>),
    <sys>array</sys>(<str>'type'</str>=><str>'status'</str>)
));</pre>

        <p>You can continue to connect, the connection is the same two-dimensional array, three objects corresponding to the original table and the new table, unrelated to empty, the last empty array can be <code>omitted</code></p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(),
    <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
<note>//  array()</note>
));</pre>
        <p>And so on, in theory, any number of association tables can be established</p>

        <p>There are two ways to write the parameters. The upper is the position corresponding table, and the other can be corresponding to the <code>alias</code>. The <code>alias</code> is the string before DAO</p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name` and `user`.`mId` = `message`.`id`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <str>'project'</str> => <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
    <str>'user'</str> => <sys>array</sys>(<str>'mId'</str>=><str>'id'</str>),
));</pre>


        <p>Multiple tables can also use method <code>query</code>, <code>find</code>, <code>count</code> and other query statements. The parameter is changed into <code>two dimensional array</code>.</p>
        <p> The same as the table parameter, there are two ways to write the parameters, one is the position corresponding table,
            the other is the <code>alias</code> corresponding table, and the same can also be mixed use.</p>
        <pre class="code"><note>// SELECT `user`.`id` AS 'uId', `user`.`cash`, `project`.`createTime` FROM ...</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>query</func>(<sys>array</sys>(
      <sys>array</sys>(<str>'id'</str>=><str>'uId'</str>, <str>'cash'</str>),
      <str>'project'</str> => <sys>array</sys>(<str>'createTime'</str>),
    ));</pre>

        <p>The join table conditions sometimes need to be equal to the fixed value, and can be added by the <code>on</code> method</p>
        <pre class="code"><note>// ... on `user`.`projectId` = `project`.`id` and `user`.`type` = 10 and `project`.`cash` > 100</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>on</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>10),
        <sys>array</sys>(<str>'cash'</str>=><sys>array</sys>(<str>'>'</str>, 100)),
    ))-><func>query</func>();</pre>

        <p>Multi table query and modification (<code>update</code>), and single table operation is basically the same,
            we need to pay attention to the single table parameters for <code>one-dimensional array</code>, multi table is a <code>two-dimensional array</code>, wrong will lead to execution failure.</p>


        <h2 id="dao-filter">Seletor</h2>

        <p>The DAO class can call filter (and selector), merge (or selector), and the effect is equivalent to filtering the data inside the table</p>
        <p>The same selector supports single table and multi table operations,
            in which the single table is a <code>one-dimensional array</code>, and the multi table is a <code>two-dimensional array</code></p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `user`.`type` = 'admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>The <code>merge</code> (or selector) is used to select the condition, and the condition is connected by <code>or</code></p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 OR `user`.`type` = 'admin'</note>
<prm>$merge</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>Similarly, multiple table parameters can also be used with the <code>alias</code> table, and the usage is consistent with the above</p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `project`.`type` = 'outer'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'id'</str>=><str>1</str>),
        <sys>array</sys>(<str>'type'</str>=><str>'outer'</str>),
    ));</pre>

        <p>The <code>$filter</code> condition can continue to call the <code>filter</code>/<code>merge</code> method, and the condition will continue to filter on the original basis</p>
        <pre class="code"><note>// ... WHERE (...) OR (`user`.`name` = 'test')</note>
<prm>$filter</prm> = <prm>$filter</prm>-><func>merge</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>);</pre>

        <p><code>$filter</code> can also be used as parameters to the <code>filter</code>/<code>merge</code> method. The effect is conditional superposition.</p>
        <pre class="code"><note>// ... WHERE (`user`.`id` = 1 AND `user`.`type` = 'admin') OR (`user`.`id` = 2 AND `user`.`type` = 'user')</note>
<prm>$filter1</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);
<prm>$filter2</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>2, <str>'type'</str>=><str>'user'</str>));
<prm>$merge</prm> = <prm>$filter1</prm>-><func>merge</func>(<prm>$filter2</prm>);</pre>

        <p>The <code>DAO</code> of the condition itself must be consistent with the <code>DAO</code> of the selected object,
            whether it is a <code>and selector</code> or a <code>or selector</code>, and the condition itself as a parameter. Otherwise, the exception will throw an <code>exception</code></p>

        <p>It is worth noting that the order of <code>filter</code> and <code>merge</code> has an impact on conditional screening</p>
        <p>for example:</p>
        <pre class="code"><note>// WHERE (`user`.`id`=1 AND `user`.`type`='admin') OR `user`.`id`=2</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>)-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2));

<note>// WHERE `user`.`id`=2 AND (`user`.`id`=1 AND `user`.`type`='admin')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2))-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);</pre>

        <p>As you can see from the above example, the correlation between the additions is consistent with the <code>following selector</code> expressions</p>

        <p><code>Selector</code> is the same way to get data as <code>DAO</code>, single table selector has all methods like <code>query, update, delete </code>etc.
            and multi table has methods like <code>query, update</code> etc</p>
        <pre class="code"><note>// UPDATE `DATABASE`.`TABLE` AS `user` SET `user`.`name` = 'test' WHERE `user`.`id` = 1</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1)-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>));

<note>// SELECT * FROM ... WHERE `project`.`type` = 'admin'</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(<sys>array</sys>(),<sys>array</sys>(<str>'type'</str>=><str>'admin'</str>)))
    -><func>query</func>();</pre>

        <p>Whether it is <code>filter</code> or <code>merge</code>, before the execution of the SQL statement will not be executed, will not increase the burden of SQL, you can rest assured to use.</p>

        <h2 id="dao-extracts">Complex select</h2>
        <p>In addition to the normal match selection, other complex selectors are also provided in <code>filter</code> and <code>merge</code>.</p>
        <p>If in array the value is a <code>array</code>, will automatically become the <code>in</code> statement</p>
        <pre class="code"><note>// WHERE `user`.`type` IN (1,2,3,'test')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));</pre>

        <p>Others include <code>></code>,<code><</code>,<code>>=</code>,<code><=</code>,<code>!=</code>,<code><></code>,<code>is</code>,<code>is not</code>, similarly, multi table cases with two-dimensional array to package</p>
        <pre class="code"><note>// WHERE `user`.`id` >= 10 AND `user`.`time` >= 1461584562 AND `user`.`type` is not null</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'>='</str>=><sys>array</sys>(<str>'id'</str>=>10, <str>'time'</str>=>1461584562),
    <str>'is not'</str>=><sys>array</sys>(<str>'type'</str>=><sys>NULL</sys>),
));

<note>// WHERE `user`.`id` != 3 AND `user`.`id` != 4 AND `user`.`id` != 5</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'!='</str>=><sys>array</sys>(<str>'id'</str>=><sys>array</sys>(3, 4, 5))
));</pre>

        <p>In addition, the <code>like statement</code> is also supported, which matches the beginning and end of the regular symbol:</p>
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

        <p><code>not in</code> grammar is temporarily not support, can use multiple <code>!=</code> or <code><></code> instead.</p>

        <p>At the same time, <code>filter/merge</code> can also be called iteratively to cope with complex queries with uncertain filtering conditions</p>
        <pre class="code"><note>// Action file</note>
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
<note>// get count by selector</note>
<prm>$count</prm> = <prm>$DAO</prm>-><func>count</func>();
<note>// Get the first 10 data of composite condition</note>
<prm>$data</prm> = <prm>$DAO</prm>-><func>limit</func>(10)-><func>query</func>();</pre>

        <h2 id="dao-group">other conditions</h2>
        <p>The conditional method can be called in <code>DAO</code> or <code>selector</code>, and the method can be called by transfer. The conditions in the same method are automatically merged</p>
        <p>These include <code>group</code>,<code>addition</code>,<code>order</code>,<code>limit</code>,<code>having</code></p>
        <pre class="code"><note>// SELECT `user`.`id`, avg(`user`.`cash`) AS 'a_c' FROM `TABLE` `user` WHERE ...
        GROUP BY `user`.`id`,`user`.`type` HAVING `a_c` >= 1000 ORDER BY `a_c` DESC, `id` ASC LIMIT 20,10;</note>
<prm>$this</prm>-><prm>userDAO</prm> <note>//->filter(...)</note>
    -><func>addition</func>(<sys>array</sys>(<str>'avg'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'a_c'</str>))
    -><func>group</func>(<sys>array</sys>(<str>'id'</str>, <str>'type'</str>))
    -><func>having</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'a_c'</str>, 1000)))
    -><func>order</func>(<sys>array</sys>(<str>'a_c'</str>=><str>'DESC'</str>, <str>'id'</str>=><str>'ASC'</str>))
    <note>// limit The first parameter is the number of entries, and the second parameter is the starting position (default is 0)</note>
    -><func>limit</func>(10, 20)
    -><func>query</func>(<sys>array</sys>(<str>'id'</str>));</pre>

        <p><code>addition</code> is a method of data processing, providing <code>max</code>,<code>count</code>,<code>sum</code>,<code>min</code>,<code>avg</code> and other computing methods</p>
        <p><code>two dimensional array</code> is also required for multiple tables</p>
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

        <p>Each condition is independent, and does <code>not affect</code> the original DAO or selector, you can use it safely</p>

        <pre class="code"><note>// This object is not changed by adding conditions</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));
<note>// 2</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>limit</func>(2)-><func>count</func>()
<note>// 4</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>count</func>()
<note>// 100 (total number of rows in user table)</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>count</func>()</pre>


        <h2 id="dao-command">SQL template</h2>
        <p>The framework provides the <code>selector</code>, <code>conditional statements</code>, <code>contingency table</code>, basically covering all the SQL syntax,
            but there may be some uncommon usage cannot be achieved, so it provides a way to use SQL templates, support for user-defined SQL statement, but <node>not recommended for the user</node>,
            if you must use it. Please be sure to do a good job in their own <code>anti SQL injection</code></p>

        <p>There are two ways to use,<code>select</code> (query return data), and <code>command</code> (execute return bool or affected rows)</p>
        <p>It will automatically replace column <code>:where</code>,<code>:table</code>,<code>:order</code>,<code>:group</code>,<code>:addition</code></p>
        <pre class="code"><note>// select * from `DATABASE`.`TABLE` WHERE ...</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select * from :table WHERE ...;'</str>);

<note>// update `DATABASE`.`TABLE` `user` set name = 'test' WHERE `user`.`id` = 10 AND type = 2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>10))
    -><func>command</func>(<str>'update :table set name = 'test' WHERE :where AND type = 2;'</str>)

<note>// select id,sum(`cash`) as 'cash' from `DATABASE`.`TABLE` WHERE `id`>10
    GROUP BY `type` HAVING `cash`>=100 ORDER BY `id` desc;</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'>'</str>=><sys>array</sys>(<str>'id'</str>=>10)))
    -><func>group</func>(<sys>array</sys>(<str>'type'</str>))-><func>having</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'cash'</str>=>100)))-><func>order</func>(<sys>array</sys>(<str>'id'</str>=><str>'desc'</str>))
    -><func>addition</func>(<sys>array</sys>(<str>'sum'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'cash'</str>)))
    -><func>select</func>(<str>'select id,:addition from :table WHERE :where :group :order;'</str>);</pre>

        <p>You can also add some custom variables that automatically <code>SQL escape</code> to prevent <code>SQL injection</code></p>
        <p>The key placeholder is <code>;</code>, for example <code>;key</code>, value placeholder is <code>:</code>, for example<code>:value</code></p>
        <pre class="code"><note>// select `name` from `DATABASE`.`TABLE` WHERE `name`=2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;key from :table WHERE ;key=:value;'</str>, <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=>2));</pre>

        <p>At the same time, the replacement content can also be an array, and the system will automatically replace the string to be connected with <code>,</code></p>
        <pre class="code"><note>// select `id`,`name` from `DATABASE`.`TABLE` WHERE `name` in (1,2,3,'test')</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;fields from :table WHERE ;key in (:value);'</str>,
    <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>), <str>'fields'</str>=><sys>array</sys>(<str>'id'</str>, <str>'name'</str>)));</pre>

        <p>The above replacement methods will be <code>SQL escape</code>, it is recommended that users use template replacement,
            rather than their own variables into the SQL statement, to prevent <code>SQL injection</code></p>

        <h2 id="dao-cursor">Cursor data</h2>
        <p>If the data taken out of DB is very large, and PHP cannot afford such a large amount of memory to process it, the <code>cursor</code> method is needed at this time</p>
        <p>The cursor can take out the data of the complex condition one by one and batch processing in the program, so as to reduce the memory bottleneck caused by the big data</p>
        <pre class="code"><note>// Selector, the conditional class schema is exactly the same, using the cursor method when the data is acquired</note>
<prm>$rs</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))-><func>cursor</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>));
<note>//Take out data one by one by TXDatabase::step ,e.g: ['id'=>2, 'name'=>'test']</note>
<sys>while</sys> (<prm>$data</prm>=TXDatabase::<func>step</func>(<prm>$rs</prm>)){
    <note>do something...</note>
}</pre>

        <p>If you use the SQL template, you can also pass the third parameter <code>TXDatabase::FETCH_TYPE_CURSOR</code> to achieve the use of cursors</p>
        <pre class="code"><note>// the same to above</note>
<prm>$rs</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'type'</str>=>1))
  -><func>select</func>(<str>'SELECT * FROM :table WHERE :where AND status=:status'</str>, <sys>array</sys>(<str>'status'</str>=>2), TXDatabase::<prm>FETCH_TYPE_CURSOR</prm>);
<note>// Take out data one by one by TXDatabase::step, e.g: ['id'=>2, 'name'=>'test', 'type'=>1, 'status'=>2]</note>
<sys>while</sys> (<prm>$data</prm>=TXDatabase::<func>step</func>(<prm>$rs</prm>)){
    <note>do something...</note>
}</pre>

        <h2 id="dao-transaction">SQL transaction</h2>
        <p>The framework provides a simple transaction processing mechanism for DAO, which is closed by default and can be opened by <code>TXDatebase::start()</code> method</p>
        <p><code>Note</code>: make sure that the linked data table is the storage engine of <code>innodb</code>, and that the transaction does not work.</p>

        <p>After <code>TXDatebase::start()</code>, you can commit and save the entire transaction by <code>TXDatebase::commit()</code>, but it doesn't affect the operation before <code>start</code></p>
        <p>Similarly, you can roll back the entire transaction through <code>TXDatebase::rollback()</code> and rollback all the uncommitted transactions</p>
        <p>When the program calls <code>TXDatebase::end()</code> method, the transaction will all terminate, the uncommitted transaction will be automatically rolled back; in addition, when the program is structured, it will automatically roll back the uncommitted transaction</p>

        <pre class="code"><note>// before the start of transaction will be submitted, num:0</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>0]);
<note>// start transaction</note>
TXDatabase::<func>start</func>();
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<note>// rollback transaction</note>
TXDatabase::<func>rollback</func>();
<note>// the num is still 0</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>[<str>'+'</str>=>1]]);
<note>// commit transaction</note>
TXDatabase::<func>commit</func>();
<note>// num = 2</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// close transaction</note>
TXDatabase::<func>end</func>();</pre>

        <p>In addition, transaction opening does not affect the <code>select</code> operation, only to increase, delete, modify operations have an impact</p>

        <h2 id="dao-cache">Data cache</h2>
        <p>The frame here for <code>PK key index</code> data can be cached by inheriting <code>baseDAO</code>, default is <code>closed</code>,
            and define <code>$_pkCache = true</code> in DAO to open</p>
        <p>Then you need to make table key values in DAO, and compound indexes need to pass <code>array</code>, such as <code>['id','type']</code></p>
        <p>Because the system cache defaults to <code>redis</code>, it is necessary to configure the corresponding redis configuration of the <code>/app/config/dns_xxx.php</code></p>
        <pre class="code"><note>// testDAO</note>
<sys>namespace</sys> app\dao;
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <sys>protected</sys> <prm>$dbConfig</prm> = [<str>'database'</str>, <str>'slaveDb'</str>];
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// table primary key, double keys such as ['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// enable pk cache</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;
}</pre>

        <p><code>baseDAO</code>中提供了<code>getByPk</code>,<code>updateByPk</code>,<code>deleteByPk</code>方法,
        <p><code>baseDAO</code> provides <code>getByPk</code>,<code>updateByPk</code>,<code>deleteByPk</code> methods,
            when the <code>$_pkCache</code> parameter is <code>true</code>, the data will go cache, speed up the data reading speed.</p>

        <p><code>getByPk</code> Read the PK data, return one-dimensional array data</p>
        <pre class="code"><note>// parameter is PK value, return ['id'=>10, 'name'=>'test', 'time'=>1461845038]</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>getByPk</func>(10);

<note>// Compound PK needs to pass array</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>getByPk</func>(<sys>array</sys>(10, <str>'test'</str>));</pre>

        <p><code>updateByPk</code> update row by PK</p>
        <pre class="code"><note>// parameters is PK value, update array and return true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>updateByPk</func>(10, <sys>array</sys>(<str>'name'</str>=><str>'test'</str>));</pre>

        <p><code>deleteByPk</code> delete row by PK</p>
        <pre class="code"><note>// parameters is PK value, return true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>deleteByPk</func>(10);</pre>

        <p><code>Note</code>: open <code>$_pkCache</code> DAO does not allow <code>update</code> and <code>delete</code> methods to be used again,
            which can lead to a phenomenon of cache and data asynchrony.</p>
        <p>If the table frequently deletion data, the proposed closure of the <code>$_pkCache</code> parameter,
            or in the data deletion and then call <code>clearCache()</code> method to clear the cache, so as to keep pace with the contents of the database.</p>


        <h2 id="dao-log">SQL debugging</h2>
        <p>SQL debugging method has been integrated in the framework event,
            only need to debug the statement before the method called <code>TXEvent::on(onSql)</code>, you can output the SQL statement in the <code>page console</code></p>
        <pre class="code"><note>// The one method is bound to an incident, automatic release after one output</note>
TXEvent::<func>one</func>(<const>onSql</const>);
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();

<note>// The on method binds the event until the off method is called</note>
TXEvent::<func>on</func>(<const>onSql</const>);
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
TXEvent::<func>off</func>(<const>onSql</const>);</pre>

        <p>The SQL event function can also be bound by itself, and the specific usage will be expanded in detail later in the <code>event</code></p>
    </div>

    <div class="bs-docs-section">
        <h1 id="view" class="page-header">Page render</h1>
        <p>Please open <code>short_open_tag</code> in <code>php.ini</code> configuration and use simplified template to improve development efficiency</p>
        <p>The view directory of the page is under <code>/app/template/</code>, and can be returned in the <code>Action</code> layer by the <code>$this->display()</code> method</p>
        <p>The general <code>Action</code> class extends the <code>baseAction</code> class. In <code>baseAction</code>,
            some common page parameters can be issued together to reduce development and maintenance costs</p>

        <h2 id="view-param">Param render</h2>
        <p>The <code>display</code> method has three parameters, the first one is the specified <code>template</code> file,
            the second is the page parameter array, and the third is the system class data (default is empty array).</p>
        <pre class="code"><note>// return /app/template/main/test.tpl.php </note>
<sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <sys>array</sys>(<str>'test'</str>=>1), <sys>array</sys>(<str>'path'</str>=><str>'/test.png'</str>));

<note>/* /app/template/main/test.tpl.php
return:
&lt;div class="container">
    &lt;span> 1  &lt;/span>
    &lt;img src="/test.png"/>
&lt;/div> */</note>
<act>&lt;div</act> class="<func>container</func>"<act>&gt;</act>
    <act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
    <act>&lt;img</act> src="<sys>&lt;?=</sys><prm>$path</prm><sys>?&gt;</sys>"<act>/&gt;</act>
<act>&lt;/div&gt;</act></pre>

        <p>The data of the second parameters will be placed in the <code>$PRM</code> page object.
            The third parameter is rendered directly, which is suitable for <code>static resource addresses</code> or <code>class data</code></p>

        <h2 id="view-tkd">Custom TDK</h2>
        <p>The page TKD is generally defined by default in <code>common.tpl.php</code>. If the page needs to modify the corresponding <code>title,keywords,description</code>,
            it can be assigned after the <code>TXResponse</code> is generated</p>
        <pre class="code"><prm>$view</prm> = <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <prm>$params</prm>);
<prm>$view</prm>-><prm>title</prm> = <str>'Biny'</str>;
<prm>$view</prm>-><prm>keywords</prm> = <str>'biny,php,framework'</str>;
<prm>$view</prm>-><prm>description</prm> = <str>'Biny is a tiny, high-performance PHP framework for web applications'</str>;
<sys>return</sys> <prm>$view</prm>;</pre>

        <h2 id="view-xss">Anti-XSS</h2>
        <p>Using the framework <code>display</code> method, the parameter <code>HTML instantiation</code> is automatically implemented to prevent <code>XSS injection</code>.</p>
        <p>There are two ways to get the parameters in <code>$PRM</code>. The general array content is obtained, and it will be <code>transferred</code> automatically</p>
        <pre><note>// display &lt;div&gt;, Source code is &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>In addition, you can use the private parameters to obtain, but will not be escaped,
            suitable for the need to display the full page structure requirements (<code>ordinary pages are not recommended to use, the hidden danger is great</code>)</p>
        <pre><note>// display &lt;div&gt;, Source code is &lt;div&gt; </note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><prm>test</prm><sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<note>// Same effect</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><func>get</func>(<str>'test'</str>)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>In a multi tier data structure, it can also be used recursively</p>
        <pre><note>// display &lt;div&gt;, source code is &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>][<str>'key1'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>get</func>(0)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>The array parameters of multi layer structure will be <code>automatically escaped</code> when used, and will not be escaped when they are not used, so as to avoid waste of resources and affect rendering efficiency.</p>

        <p><code>Note</code>: the third parameter is <code>HTML instantiation</code> or not, can be configuration by the field <code>objectEncode</code> in <code>/config/config.php</code>.</p>

        <h2 id="view-func">Param function</h2>
        <p>In addition to rendering, render parameters also provide some original <code>array</code> methods, for example:</p>
        <p><code>in_array</code></p>
        <pre class="code"><note>// equal in_array('value', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>in_array</func>(<str>'value'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p><code>array_key_exists</code></p>
        <pre class="code"><note>// equal array_key_exists('key1', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>array_key_exists</func>(<str>'key1'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>Other methods and so on, the use of the same way, such as <code>json_encode</code></p>
        <pre><note>// Assign parameters to JS, var jsParam = {'test':1, "demo": {"key": "test"}};</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>json_encode</func>()<sys>?&gt;</sys>;</pre>

        <p>Determine whether the array parameter is empty, you can directly call <code>$PRM['array']()</code> or <code>$PRM('array')</code> method to judge,
            the effect is equivalent to <code>!empty()</code> method</p>
        <pre class="code"><note>// equals if (!empty($array))</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>（<str>'array'</str>)) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>Other parameter methods can be defined by themselves in <code>/lib/data/TXArray.php</code></p>
        <p>For example, define a <code>len</code> method and return the length of the array</p>
        <pre class="code"><note>/lib/data/TXArray.php</note>
<sys>public function</sys> <act>len</act>()
{
    <sys>return count</sys>(<prm>$this</prm>-><prm>storage</prm>);
}</pre>
        <p>and then can used in <code>tpl</code></p>
        <pre><note>// Assign parameters to JS, var jsParam = 2;</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>len</func>()<sys>?&gt;</sys>;</pre>

    </div>

    <div class="bs-docs-section">
        <h1 id="event" class="page-header">Event</h1>
        <p>The event mechanism is provided in the framework, which is convenient for global call.
            Among them, the system default has providedcode>beforeAction</code>,<code>afterAction</code>,<code>onException</code>,<code>onError</code>,<code>onSql</code> these</p>
        <p><code>beforeAction</code> executed before the execution of Action (triggered after init() method)</p>
        <p><code>afterAction</code> executed after the execution of Action (triggered before rendering page)</p>
        <p><code>onException</code>When the system throws an exception, it is triggered, and the error code is passed, and code is defined in <code>/config/exception.php</code></p>
        <p><code>onError</code>When the program calls the <code>$this->error($data)</code> method, it is triggered to pass the <code>$data</code> parameter</p>
        <p><code>onSql</code>The execution of the statement is triggered, and the <code>TXEvent::on(onSql)</code> in the above example uses this event</p>

        <h2 id="event-init">Defining events</h2>
        <p>The system provides two ways to define events, one is to define long events <code>$fd = TXEvent::on($event, [$class, $method])</code>, and that it will take effect until off.</p>
        <p>Parameters are <code>event names</code>, <code>methods[class, method name]</code> method can not pass, default is <code>TXLogger::event()</code> method, will print in page console</p>
        <p><code>$fd</code> returns the operator of the event. When invoking the off method, the event can be bound by passing the operator.</p>

        <pre class="code"><sys>namespace</sys> app\controller;
<note>/**
* main Action
* @property \app\service\testService $testService
*/  </note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>// init</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>// To trigger the beforeAction event, it can be defined in init and will be triggered after init</note>
        TXEvent::<func>on</func>(<const>beforeAction</const>, <sys>array</sys>(<prm>$this</prm>, <str>'test_event'</str>));
    }

    <note>// default route index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// Binding the my_event1 method and the my_event2 method in testService to the myEvent event, the two methods are executed and executed in the order of binding</note>
        <prm>$fd1</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event1'</str>));
        <prm>$fd2</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event2'</str>));

        <note>// do something ..... </note>

        <note>// unbind method my_event1 in event myEvent</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>, <prm>$fd1</prm>);

        <note>// unbind all events myEvent, all the myEvent events will not executed again</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>);

        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'for test'</str>);
    }

    <note>// custom event</note>
    <sys>public function</sys> <act>test_event</act>(<prm>$event</prm>)
    {
        <note>// addLog is the method to write log</note>
        TXLogger::<func>addLog</func>(<str>'trigger beforeAction event'</str>);
    }
}</pre>

        <p>Another binding is a single binding event <code>TXEvent::one()</code>, which calls the same parameter and returns the <code>$fd</code> operator,
            which is automatically bound when the event is triggered once</p>
        <pre><prm>$fd</prm> = TXEvent::<func>one</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>));</pre>

        <p>Of course, if you want to bind multiple but not long term bindings, the system also provides a <code>bind</code> method with similar parameter usage.</p>
        <pre><note>// The first parameter binding method, the second is the event name, the third is the number of bindings,
    and the trigger number is automatically released after the full number of times.</note>
<prm>$fd</prm> = TXEvent::<func>bind</func>(<sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>), <str>'myEvent'</str>, <prm>$times</prm>);</pre>

        <h2 id="event-trigger">Trigger events</h2>
        <p>Users can customize events and trigger selectively, and can directly use <code>TXEvent::trigger($event, $params)</code> method</p>
        <p>There are two parameters, the first is the trigger event name, and the second is the trigger transfer parameter, which will be passed to the trigger method</p>
        <pre class="code"><note>// trigger myEvent event</note>
TXEvent::<func>trigger</func>(<str>'myEvent'</str>, <sys>array</sys>(<func>get_class</func>(<prm>$this</prm>), <str>'test'</str>))

<note>// the method defined in bind event</note>
<sys>public function</sys> my_event(<prm>$event</prm>, <prm>$params</prm>)
{
    <note>// array('testService', 'test')</note>
    <sys>var_dump</sys>(<prm>$params</prm>);
}</pre>

    </div>

    <div class="bs-docs-section">
        <h1 id="forms" class="page-header">Form Validation</h1>
        <p>Biny provides a complete set of form validation solutions that apply to most scenarios.</p>
        <p>Form validation supports all types of validation and custom methods</p>
        <p>for example:</p>
        <pre class="code">
<sys>namespace</sys> app\form;
<sys>use</sys> biny\lib\TXForm;
<note>/**
 * @property \app\service\testService $testService
 * A custom form validation class extends TXForm
 */</note>
<sys>class</sys> testForm <sys>extends</sys> TXForm
{
    <note>// Define form parameters, types, and default values (default null)</note>
    <sys>protected</sys> <prm>$_rules</prm> = [
        <note>// id must be int, default is 10</note>
        <str>'id'</str>=>[<sys>self</sys>::<prm>typeInt</prm>, 10],
        <note>// name most not empty (include null, empty string)</note>
        <str>'name'</str>=>[<sys>self</sys>::<prm>typeNonEmpty</prm>],
        <note>// Custom verification method (valid_testCmp)</note>
        <str>'status'</str>=>[<str>'testCmp'</str>]
    ];

    <note>// Custom verification method</note>
    <sys>public function</sys> <act>valid_testCmp</act>()
    {
        <note>// Can call Service and DAO like Action layer</note>
        <sys>if</sys> (<prm>$this</prm>-><prm>testService</prm>-><func>checkStatus</func>(<prm>$this</prm>-><prm>status</prm>)){
            <note>// pass the validation</note>
            <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
        } <sys>else</sys> {
            <note>// failure, the error message can get by getError method</note>
            <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'Illegal type'</str>);
        }
    }
}</pre>
        <p>After define the validation class, you can use it in Action, and you can load the form through the <code>getForm</code> method</p>
        <pre class="code"><note>// load testForm</note>
<prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>);
<note>// verificate form column, true/false</note>
<sys>if</sys> (!<prm>$form</prm>-><func>check</func>()){
    <note>// get error message</note>
    <prm>$error</prm> = <prm>$form</prm>-><func>getError</func>();
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<prm>$error</prm>);
}
<note>// Get form parameter</note>
<prm>$status</prm> = <prm>$form</prm>-><prm>status</prm>;
<note>// Get form all data, return as array ['id'=>1, 'name'=>'billge', 'status'=>2]</note>
<prm>$data</prm> = <prm>$form</prm>-><func>values</func>();
        </pre>

        <p><code>Note</code>: undefined fields in <code>$_rules</code> cannot be obtained in <code>$form</code>, and even without verification, it is best to define them</p>
        <p>In many cases, the form parameters are not all the same, and the system supports <code>Form reuse</code>, that is, you can customize some of the content in the generic Form class</p>
        <p>For example, testForm, the example above, has a similar form, but it has one field type, and it needs to change the way status is validated</p>
        <p>You can add a method in testForm</p>
        <pre class="code"><note>// method in testForm</note>
<sys>public function</sys> <act>addType</act>()
{
    <note>// add type column, default 'default', rule is not empty</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'type'</str>] = [<sys>self</sys>::<prm>typeNonEmpty</prm>,<str>'default'</str>];
    <note>// Modify the status judgment condition, changed to valid_typeCmp() method validation, remember to write this method</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'status'</str>][0] = <str>'typeCmp'</str>;
}</pre>

        <p>Then loading the form in Action also requires adding <code>'addType'</code> as a parameter, and others use the same method</p>
        <pre class="code"><prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>, <str>'addType'</str>);</pre>

        <p>You can write multiple additional methods in a form validation class, and they don't have any impact on each other directly</p>

        <h2 id="forms-type">Verification type</h2>
        <p>The system provides 7 default authentication methods. When the verification fails, the error information is recorded, and the user can obtain it through the <code>getError</code> method</p>
        <p><code>self::typeInt</code> Numeric types, including integer floating point types, negative numbers</p>
        <p><code>self::typeBool</code> Determine whether it is true/false</p>
        <p><code>self::typeArray</code> Determine whether the array type</p>
        <p><code>self::typeObject</code> Determine whether it is an object data</p>
        <p><code>self::typeDate</code> Determine whether it is a legitimate date</p>
        <p><code>self::typeDatetime</code> Determine whether it is a legitimate datetime</p>
        <p><code>self::typeNonEmpty</code> Determine whether the non empty (including null, empty string)</p>
        <p><code>self::typeRequired</code> With this parameter, it can be an empty string</p>

        <p>The verification type covers almost all cases. If there is a type that cannot be satisfied, the user can customize the validation method, which is no longer explained in the above examples</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="debug" class="page-header">Debug</h1>
        <p>There are two debugging methods in Biny, one is debugging in the page console, and the other is convenient for the user to debug the corresponding web page.</p>
        <p>The other is debugging in the log, just like any other framework</p>

        <h2 id="debug-console">Console Debug</h2>
        <p>A major feature of Biny is the way the console is debugged, users can debug the data they want, and it doesn't have an impact on the current page structure.</p>
        <p>The debug switch is in <code>/web/index.php</code></p>
        <pre class="code"><note>// Console debugging, after the closure of the console will not output content</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);</pre>
        <p>Synchronous and asynchronous can also debug, but asynchronous debugging is the need to refer to the <code>/static/js/main.js</code> file,
            so asynchronous Ajax request will also debug information output in the console.</p>

        <p>Debugging method is very simple, the global can call <code>TXLogger::info($message, $key)</code>, in addition to warn, error, log and so on</p>
        <p>The first parameter is the content that you want to debug, and also supports the array, the output of the Object class. The second parameter is debugging key, default is <code>phpLogs</code></p>
        <p><code>TXLogger::info()</code> info debug</p>
        <p><code>TXLogger::warn()</code> warning debug</p>
        <p><code>TXLogger::error()</code> error debug</p>
        <p><code>TXLogger::log()</code> log debug</p>
        <p>Here's a simple example, and the output of the console. The results will be different because browsers are different, and the effect is the same.</p>

        <pre class="code"><note>// can use anywhere in framework</note>
TXLogger::<func>log</func>(<sys>array</sys>(<str>'cc'</str>=><str>'dd'</str>));
TXLogger::<func>error</func>(<str>'this is a error'</str>);
TXLogger::<func>info</func>(<sys>array</sys>(1,2,3,4,5));
TXLogger::<func>warn</func>(<str>"ss"</str>, <str>"warnKey"</str>);</pre>

        <p><img src="//f.wetest.qq.com/gqop/10000/20000/GuideImage_c5f68a0251b7f55efbbe0c47df9e757c.png"></p>

        <p>In addition, the <code>TXLogger</code> debug class also supports the output of time and memory, which can be used to optimize the performance of the code.</p>
        <pre class="code"><note>// At the beginning of the end, with time and memory, you can get the performance of the intermediate program</note>
TXLogger::<func>time</func>(<str>'start-time'</str>);
TXLogger::<func>memory</func>(<str>'start-memory'</str>);
TXLogger::<func>log</func>(<str>'do something'</str>);
TXLogger::<func>time</func>(<str>'end-time'</str>);
TXLogger::<func>memory</func>(<str>'end-memory'</str>);</pre>

        <p><img src="http://f.wetest.qq.com/gqop/10000/20000/GuideImage_c2d7aac054bd9f9cd6069445e294e826.png"></p>

        <h2 id="debug-log">Log debug</h2>

        <p>The log directory of the platform is in <code>/logs/</code>. Please make sure that the directory has <code>write permissions</code></p>
        <p>The exception record will be generated in the <code>error_{date}.log</code> file, such as: <code>error_2016-05-05.log</code></p>
        <p>Debug records will be generated in the <code>log_{date}.log</code> file, such as: <code>log_2016-05-05.log</code></p>

        <p>In the program, you can add a log by calling <code>TXLogger::addLog($log, INFO)</code>, and <code>TXLogger::addError($log, ERROR)</code> adds the exception</p>
        <p><code>$log</code> parameter supports the array and automatically prints the array</p>
        <p><code>$LEVEL</code> can use constants (<code>INFO</code>、<code>DEBUG</code>、<code>NOTICE</code>、<code>WARNING</code>、<code>ERROR</code>) default is level programe given.</p>
        <p>The system program errors will also be displayed in the error log. If the page appears 500, you can see the location in the error log</p>

    </div>

    <div class="bs-docs-section">
        <h1 id="shell" class="page-header">Shell execution</h1>
        <p>In addition to providing HTTP request processing, the Biny framework also provides a complete set of script execution logic</p>
        <p>The execution entry is the <code>shell.php</code> file in the root directory, and the user can execute by <code>php shell.php {router} {param}</code> call through the command line</p>
        <p><code>router</code> is script routing, <code>param</code> is execution parameter, can default or multiple parameters</p>
        <pre class="code"><note>// shell.php</note>
<note>// set timezone</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// enabled shell model (in shell.php is true)</note>
<sys>defined</sys>(<str>'RUN_SHELL'</str>) <sys>or</sys> <sys>define</sys>(<str>'RUN_SHELL'</str>, <sys>true</sys>);
<note>// dev pre pub environment</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
</pre>

        <h2 id="shell-router">Shell route</h2>
        <p>The routing is basically consistent with the HTTP request pattern, which is divided into <code>{module}/{method}</code> forms,
            in which <code>{method}</code> can be default and default is <code>index</code></p>
        <p>For example, <code>index/test</code> executes the <code>action_test</code> method in <code>indexShell</code>,
            and <code>demo</code> executes the <code>action_index</code> method in <code>demoShell</code></p>
        <p>If router defaults, the default reads the <code>/config/config.php</code> content in the router as the default route</p>
        <pre class="code"><note>// /config/config.php</note>
<sys>return array</sys>(
    <str>'router'</str> => <sys>array</sys>(
        <note>// http default route</note>
        <str>'base_action'</str> => <str>'demo'</str>,
        <note>// shell default route</note>
        <str>'base_shell'</str> => <str>'index'</str>
    )
)
<note>// /app/shell/indexShell.php</note>
<sys>namespace</sys> app\shell;
<sys>use</sys> biny\lib\TXShell;
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>// Like HTTP, the init method is executed first</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>//return 0 or not returned, the program continues to execute.
        If the other content is returned, the program terminates after outputting the content.</note>
        <sys>return</sys> 0;
    }

    <note>//default route index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// Returns an exception and logs and outputs it at the terminal</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'execute error'</str>);
    }
}
</pre>

        <h2 id="shell-param">Shell Param</h2>
        <p>The script executes the arguments that can pass the complex number, and the HTTP request can be caught directly in the method.
            The order is consistent with the parameter order, and can be default</p>
        <p>In addition, the <code>param</code> method can be used to obtain the parameters of the corresponding position</p>
        <p>For example, the terminal executes <code>php shell.php test/demo 1 2 aaa</code>, and the results are as follows:</p>
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

        <p>At the same time, the framework also provides variable parameter delivery, which is consistent with the HTTP schema</p>
        <p>For example, the terminal executes the <code>php shell.php test/demo --name="test" --id=23 demo</code>, and the results are as follows:</p>
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

        <note>// Variables without arguments begin in order from zeroth bits</note>
        <note>// demo</note>
        <sys>echo</sys> <prm>$this</prm>-><func>param</func>(0);
    }
}</pre>
        <p><code>Note</code>: the use of variable transmission method, the default parameters will not capture the non parameter variables,
            such as the above <code>demo</code> needs to get through <code>param</code> method</p>

        <h2 id="shell-log">Shell log</h2>
        <p>Script execution no longer has other functions of HTTP mode, such as <code>form validation</code>, <code>page rendering</code>, <code>browser console debugging</code>.
            So in <code>TXLogger</code> debugging class, <code>info/error/debug/warning</code> these methods will be changed to the terminal output</p>
        <p>And can also call <code>TXLogger::addLog</code> and <code>TXLogger::addError</code> to write log operations</p>
        <p>The log directory is saved in the <code>/logs/shell/</code> directory. Please ensure that the directory has <code>write permissions</code>. The format is consistent with the HTTP pattern</p>
        <p><code>Note</code>: when the program returns to <code>$this->error($msg)</code>, the system will default to call <code>TXLogger::addError($msg)</code>, please do not repeat the call.</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="other" class="page-header">Others</h1>
        <p>Many single instances of the system can be directly obtained by <code>TXApp::$base</code></p>
        <p><code>TXApp::$base->request</code> is the current request, access to the current address, client IP, etc.</p>
        <p><code>TXApp::$base->cache</code> is request static cache, valid only in the current request</p>
        <p><code>TXApp::$base->session</code> is system session, can be directly obtained and copied, set the expiration time</p>
        <p><code>TXApp::$base->memcache</code> is system Memcache, can be directly obtained and copied, set the expiration time</p>
        <p><code>TXApp::$base->redis</code> is system redis, can be directly obtained and copied, set the expiration time</p>

        <h2 id="other-request">Request</h2>
        <p>After entering the <code>Controller</code> layer, <code>Request</code> can be called. Here are a few common operations</p>
        <pre class="code"><note>// for example with /test/demo/?id=10 </note>

<note>// Get Action name, return test</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getModule</func>();

<note>// Get Action class, return testAction</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getModule</func>(<sys>true</sys>);

<note>// Get Method name return action_demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>();

<note>// Get Method name without 'action' return demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>(<sys>true</sys>);

<note>// Is asynchronous request or not returnfalse</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>isAjax</func>();

<note>// Return relative path,  /test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>();

<note>// Return absolute path, http://www.billge.cc/test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>(<sys>true</sys>);

<note>// Return url with querys,  /test/demo/?id=10</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUrl</func>();

<note>// Return url refer (last page url)</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getReferrer</func>();

<note>// Get page user agent</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserAgent</func>();

<note>// Get client ip</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserIP</func>();</pre>

        <h2 id="other-cache">Cache</h2>
        <p>Biny provides a global cache in the <code>lifetime of the program</code>, which is very simple to use</p>
        <pre class="code"><note>// Only need to assign, can realize the cache setting</note>
TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testkey</prm> = <str>'test'</str>;
<note>// The acquisition takes the element directly, and null returns if it does not exist</note>
<prm>$testKey</prm> = TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testkey</prm>;</pre>

        <p>At the same time, Cache also supports <code>isset</code> judgment and <code>unset</code> operation</p>
        <pre class="code"><note>// return true/false</note>
<prm>$bool</prm> = <sys>isset</sys>(TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testKey</prm>);
<note>// delete cache</note>
<sys>unset</sys>(TXApp::<prm>$base</prm>-><prm>cache</prm>-><prm>testKey</prm>);
        </pre>

        <h2 id="other-session">Session</h2>
        <p>The setting and acquisition of session are relatively simple (same as cache). The object will not be created without calling the session to avoid the loss of performance.</p>
        <pre class="code"><note>// Only need to assign, can realize the session setting</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm> = <str>'test'</str>;
<note>// The acquisition takes the element directly, and null returns if it does not exist</note>
<prm>$testKey</prm> = TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm>;</pre>

        <p>At the same time, the session can be closed by the method <code>close()</code> to avoid the deadlock problem of session</p>
        <pre class="code"><note>// Will reopen session to getting data again after close</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>close</func>();</pre>
        <p>The <code>clear()</code> method empties the contents of the current session</p>
        <pre class="code"><note>// After clear, it is null </note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>clear</func>();</pre>

        <p>At the same time, session also supports <code>isset</code> judgment</p>
        <pre class="code"><note>// return true/false</note>
<prm>$bool</prm> = <sys>isset</sys>(TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testKey</prm>);</pre>

        <h2 id="other-cookie">Cookie</h2>
        <p>The acquisition and setting of cookie are completed in <code>TXApp::$base->request</code>, respectively, providing <code>getCookie</code> and <code>setCookie</code> methods</p>

        <p><code>getCookie</code> parameter is the cookie key value that needs, if not passed, then return all cookie, return with array structure</p>
        <pre class="code"><prm>$param</prm> = TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getCookie</func>(<str>'param'</str>);</pre>
        <p><code>setCookie</code>There are 4 parameters, which are key, value, expiration time (unit seconds),
            path belonging to the cookie, the expiration date default is 1 days, the path default is <code>'/'</code></p>
        <pre class="code">TXApp::<prm>$base</prm>-><prm>request</prm>-><func>setCookie</func>(<str>'param'</str>, <str>'test'</str>, 86400, <str>'/'</str>);</pre>


        <div style="height: 200px"></div>
    </div>

</div>
<?php if (!TXApp::$base->request->isMobile()){?>
<div class="col-md-3" role="complementary">
    <nav class="bs-docs-sidebar hidden-print hidden-xs hidden-sm">
        <ul class="nav bs-docs-sidenav">

            <li>
                <a href="#overview">Overview</a>
                <ul class="nav">
                    <li><a href="#overview-introduce">Introduce</a></li>
                    <li><a href="#overview-files">Directory</a></li>
                    <li><a href="#overview-level">Call relation</a></li>
                    <li><a href="#overview-index">Environmental allocation</a></li>
                </ul>
            </li>
            <li>
                <a href="#router">Route</a>
                <ul class="nav">
                    <li><a href="#router-rule">Default rule</a></li>
                    <li><a href="#router-custom">Custom route</a></li>
                    <li><a href="#router-ajax">Ajax request</a></li>
                    <li><a href="#router-restful">Restful</a></li>
                    <li><a href="#router-param">Get Param</a></li>
                    <li><a href="#router-check">Authorization verification</a></li>
                </ul>
            </li>
            <li>
                <a href="#config">Config</a>
                <ul class="nav">
                    <li><a href="#config-system">System Config</a></li>
                    <li><a href="#config-app">App config</a></li>
                    <li><a href="#config-env">Environmental allocation</a></li>
                    <li><a href="#config-alias">Alias</a></li>
                </ul>
            </li>
            <li>
                <a href="#dao">Database</a>
                <ul class="nav">
                    <li><a href="#dao-connect">Connect</a></li>
                    <li><a href="#dao-mapped">DAO mapping</a></li>
                    <li><a href="#dao-simple">Simple Query</a></li>
                    <li><a href="#dao-update">Delete/Update</a></li>
                    <li><a href="#dao-join">Join Table</a></li>
                    <li><a href="#dao-filter">selector</a></li>
                    <li><a href="#dao-extracts">Complex select</a></li>
                    <li><a href="#dao-group">other conditions</a></li>
                    <li><a href="#dao-command">SQL template</a></li>
                    <li><a href="#dao-cursor">Cursor data</a></li>
                    <li><a href="#dao-transaction">SQL transaction</a></li>
                    <li><a href="#dao-cache">Data cache</a></li>
                    <li><a href="#dao-log">SQL debugging</a></li>
                </ul>
            </li>
            <li>
                <a href="#view">Page render</a>
                <ul class="nav">
                    <li><a href="#view-param">Param render</a></li>
                    <li><a href="#view-tkd">Custom TKD</a></li>
                    <li><a href="#view-xss">Anti-XSS</a></li>
                    <li><a href="#view-func">Param Function</a></li>
                </ul>
            </li>
            <li>
                <a href="#event">Event</a>
                <ul class="nav">
                    <li><a href="#event-init">Defining events</a></li>
                    <li><a href="#event-trigger">Trigger events</a></li>
                </ul>
            </li>
            <li>
                <a href="#forms">Form Validation</a>
                <ul class="nav">
                    <li><a href="#forms-type">Verification type</a></li>
                </ul>
            </li>
            <li>
                <a href="#debug">Debug</a>
                <ul class="nav">
                    <li><a href="#debug-console">Console Debug</a></li>
                    <li><a href="#debug-log">Log Debug</a></li>
                </ul>
            </li>
            <li>
                <a href="#shell">Shell execution</a>
                <ul class="nav">
                    <li><a href="#shell-router">Shell route</a></li>
                    <li><a href="#shell-param">Shell param</a></li>
                    <li><a href="#shell-log">Shell log</a></li>
                </ul>
            </li>
            <li>
                <a href="#other">Others</a>
                <ul class="nav">
                    <li><a href="#other-request">Request</a></li>
                    <li><a href="#other-cache">Cache</a></li>
                    <li><a href="#other-session">Session</a></li>
                    <li><a href="#other-cookie">Cookie</a></li>
                </ul>
            </li>

        </ul>
        <a class="back-to-top" href="#top">
            Return top
        </a>

    </nav>
</div>
<?php } ?>

</div>
</div>

<?php include TXApp::$view_root . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$webRoot?>/static/js/demo.js"></script>