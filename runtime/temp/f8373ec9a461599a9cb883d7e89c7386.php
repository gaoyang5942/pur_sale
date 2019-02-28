<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:77:"/usr/local/var/www/pur_sale/public/../app/admin/view/setting/clear_cache.html";i:1548653364;s:61:"/usr/local/var/www/pur_sale/app/admin/view/public/header.html";i:1548653364;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="/static/admin/themes/flatadmin/bootstrap.min.css" rel="stylesheet">
    <link href="/static/admin/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/",
            APP: '<?php echo \think\Request::instance()->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/static/admin/js/jquery-1.10.2.min.js"></script>
    <script src="/static/js/wind.js"></script>
    <script src="/static/admin/js/bootstrap.min.js"></script>
    <script src="/static/js/frontend.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip();
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>

</head>
<body>
<div class="wrap">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="panel panel-default" style="margin-top: 10%">
                <div class="panel-heading">
                    <h3 class="panel-title">缓存已更新</h3>
                </div>
                <div class="panel-body">
                    <div style="line-height: 60px;">
                        缓存已更新
                    </div>
                    <div>
                        <a href="javascript:close_app();" class="btn btn-primary pull-right">关闭</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/admin.js"></script>
<script>
    var closeTimeout = setTimeout(function () {
        parent.close_current_app();
    }, 3000);

    function close_app() {
        clearTimeout(closeTimeout);
        parent.close_current_app();
    }
</script>
</body>
</html>
