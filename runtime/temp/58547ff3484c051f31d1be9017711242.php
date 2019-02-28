<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"/usr/local/var/www/pur_sale/public/../app/admin/view/index/index.html";i:1548653362;}*/ ?>
<!DOCTYPE html>
<html lang="zh_CN" style="overflow: hidden;">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta charset="utf-8">
    <title>药店管理系统</title>
    <meta name="description" content="This is page-header (.page-header &gt; h1)">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->
    <link href="/static/admin/themes/flatadmin/bootstrap.min.css" rel="stylesheet">
    <link href="/static/admin/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css?page=index" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/static/admin/themes/flatadmin/simplebootadminindex.min.css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /*-----------------导航hack--------------------*/
        .nav-list > li.open {
            position: relative;
        }

        .nav-list > li.open .back {
            display: none;
        }

        .nav-list > li.open .normal {
            display: inline-block !important;
        }

        .nav-list > li.open a {
            padding-left: 7px;
        }

        .nav-list > li .submenu > li > a {
            background: #fff;
        }

        .nav-list > li .submenu > li a > [class*="fa-"]:first-child {
            left: 20px;
        }

        .nav-list > li ul.submenu ul.submenu > li a > [class*="fa-"]:first-child {
            left: 30px;
        }

        /*----------------导航hack--------------------*/
    </style>

    <script>
        //全局变量
        var GV = {
            HOST: "<?php echo $_SERVER['HTTP_HOST']; ?>",
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/"
        };
    </script>
</head>

<body style="min-width:900px;overflow: hidden;">
<div id="loading"><i class="loadingicon"></i><span>正在加载...</span></div>
<div id="right-tools-wrapper">
</div>
<div class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="<?php echo url('admin/index/index'); ?>" class="navbar-brand" style="min-width: 200px;text-align: center;">药店管理系统</a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse" id="navbar-main">
            <div class="pull-left" style="position: relative;">
                <a id="task-pre" class="task-changebt"><i class="fa fa-chevron-left"></i></a>
                <div id="task-content">
                    <ul class="nav navbar-nav cmf-component-tab" id="task-content-inner">
                        <li class="cmf-component-tabitem noclose" app-id="0" app-url="<?php echo url('main/index'); ?>"
                            app-name="首页">
                            <a class="cmf-tabs-item-text">首页</a>
                        </li>
                    </ul>
                    <div style="clear:both;"></div>
                </div>
                <a id="task-next" class="task-changebt"><i class="fa fa-chevron-right"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right simplewind-nav">
                <li class="light-blue" style="border-left:none;display: none;" id="close-all-tabs-btn">
                    <a id="close-wrapper" href="javacript:void(0);" title="关闭顶部菜单" style="color:#fff;font-size: 16px">
                        <i class="fa fa-times right_tool_icon"></i>
                    </a>
                </li>
                <li class="light-blue" style="border-left:none;">
                    <a id="refresh-wrapper" href="javacript:void(0);" title="刷新当前页面" style="color:#fff;font-size: 16px">
                        <i class="fa fa-refresh right_tool_icon"></i>
                    </a>
                </li>
                <li class="light-blue dropdown" style="border-left:none;">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <?php if(isset($admin['avatar']) && $admin['avatar']): ?>
                            <img class="nav-user-photo" width="30" height="30"
                                 src="<?php echo get_user_avatar_url($admin['avatar']); ?>" alt="<?php echo $admin['user_login']; ?>">
                            <?php else: ?>
                            <img class="nav-user-photo" width="30" height="30"
                                 src="/static/admin/images/logo-18.png" alt="<?php echo (isset($admin['user_login']) && ($admin['user_login'] !== '')?$admin['user_login']:''); ?>">
                        <?php endif; ?>
                        <span class="user-info">
								欢迎, <?php echo $admin['user_login']; ?>
							</span>
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
                        <!-- <?php if(auth_check(get_current_admin_id(),'admin/Setting/site')): ?>
                            <li>
                                <a href="javascript:openapp('<?php echo url('setting/site'); ?>','index_site','网站信息');"><i
                                        class="fa fa-cog"></i> 网站信息</a></li>
                        <?php endif; ?> -->
                        <!-- <?php if(auth_check(get_current_admin_id(),'admin/Setting/password')): ?>
                            <li>
                                <a href="javascript:openapp('<?php echo url('setting/password'); ?>','index_password','修改密码');"><i
                                        class="fa fa-lock"></i> 修改密码</a></li>
                        <?php endif; ?> -->
                        <li><a href="javascript:openapp('<?php echo url('setting/password'); ?>','index_password','修改密码');"><i class="fa fa-lock"></i> 修改密码</a></li>
                        <li><a href="<?php echo url('Public/logout'); ?>"><i class="fa fa-sign-out"></i> 退出</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="main-container container-fluid">

    <div class="sidebar" id="sidebar">
        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <a class="btn btn-sm btn-warning" href="/"
               title="网站首页"
               target="_blank"
               data-toggle="tooltip">
                <i class="fa fa-home"></i>
            </a>
            <?php if(auth_check(get_current_admin_id(),'admin/Setting/clearcache')): ?>
                <a class="btn btn-sm btn-danger"
                   href="javascript:openapp('<?php echo url('admin/Setting/clear_cache'); ?>','index_clearcache','清除缓存',true);"
                   title="清除缓存"
                   data-toggle="tooltip">
                    <i class="fa fa-trash-o"></i>
                </a>
            <?php endif; if(APP_DEBUG): ?>
                <a class="btn btn-sm btn-default"
                   href="javascript:openapp('<?php echo url('admin/Menu/index'); ?>','index_menu','后台菜单',true);"
                   title="后台菜单"
                   data-toggle="tooltip">
                    <i class="fa fa-list"></i>
                </a>
            <?php endif; ?>

        </div>
        <div id="nav-wrapper">
            <ul class="nav nav-list">
                <?php echo $menus; ?>
            </ul>
        </div>

    </div>

    <div class="main-content">
        <div class="page-content" id="content">
            <iframe src="<?php echo url('Main/index'); ?>" style="width:100%;height: 100%;" frameborder="0" id="appiframe-0"
                    class="appiframe"></iframe>
        </div>
    </div>
</div>

<script src="/static/admin/js/jquery-1.10.2.min.js"></script>
<script src="/static/js/wind.js"></script>
<script src="/static/admin/js/bootstrap.min.js"></script>
<script src="/static/js/admin.js"></script>
<script src="/static/admin/simpleboot3/js/adminindex.js"></script>
<script>
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
        $("li.dropdown").hover(function () {
            $(this).addClass("open");
        }, function () {
            $(this).removeClass("open");
        });
    });

    var ismenumin = $("#sidebar").hasClass("menu-min");
    $(".nav-list").on("click", function (event) {
        var closest_a = $(event.target).closest("a");
        if (!closest_a || closest_a.length == 0) {
            return
        }
        if (!closest_a.hasClass("dropdown-toggle")) {
            if (ismenumin && "click" == "tap" && closest_a.get(0).parentNode.parentNode == this) {
                var closest_a_menu_text = closest_a.find(".menu-text").get(0);
                if (event.target != closest_a_menu_text && !$.contains(closest_a_menu_text, event.target)) {
                    return false
                }
            }
            return
        }
        var closest_a_next = closest_a.next().get(0);
        if (!$(closest_a_next).is(":visible")) {
            var closest_ul = $(closest_a_next.parentNode).closest("ul");
            if (ismenumin && closest_ul.hasClass("nav-list")) {
                return
            }
            closest_ul.find("> .open > .submenu").each(function () {
                if (this != closest_a_next && !$(this.parentNode).hasClass("active")) {
                    $(this).slideUp(150).parent().removeClass("open")
                }
            });
        }
        if (ismenumin && $(closest_a_next.parentNode.parentNode).hasClass("nav-list")) {
            return false;
        }
        $(closest_a_next).slideToggle(150).parent().toggleClass("open");
        return false;
    });
</script>
</body>
</html>
