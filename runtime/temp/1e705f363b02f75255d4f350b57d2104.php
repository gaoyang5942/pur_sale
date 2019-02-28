<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"/usr/local/var/www/pur_sale/public/../app/wechat/view/index/index.html";i:1544579750;}*/ ?>
<!doctype html>
<html>

	<head>
		<meta charset="UTF-8">
		<title>药店</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link rel="stylesheet" type="text/css" href="/static/wechat/css/index.css" />
		<link rel="stylesheet" type="text/css" href="/static/wechat/css/nav.css" />
		<link rel="stylesheet" type="text/css" href="/static/wechat/css/swiper-3.4.2.min.css" />
	</head>

	<body>
		<div class="container">
			<div class="dizhi">
				<form action="" method="post">
					<a href="javascript:void(0)"  class="index_dW">请点击选择</a>
					<div class="form_search">
    						<input type="search" placeholder="搜索药店或药品" class="dizhi_search shousuo" readonly="readonly"/>
    						<a href="javascript:void(0)"> <img src="/static/wechat/img/sech_img.png" />
                        </a>
					</div>

				</form>
			</div>
			<!--轮播-->
			<div class="swiper-container">
				<div class="swiper-wrapper">
					<div class="swiper-slide"><img src="/static/wechat/img/banner.jpg" alt=""></div>
					<div class="swiper-slide"><img src="/static/wechat/img/banner.jpg" alt=""></div>
					<div class="swiper-slide"><img src="/static/wechat/img/banner.jpg" alt=""></div>
				</div>
			</div>
			<div class="fujin">
				<!--列表标头-->
				<div class="mlist-title">
					<p>附近列表</p>
				</div>
				<!--列表-->
                <div class="list">
                </div>
				<div class="clear"></div>
			</div>
		</div>
		<footer>
			<nav>
				<ul>
					<li>
						<a href="javascript:void(0)" class="logoSpanH">
							<span class="foot_logoSpan"><img src="/static/wechat/img/foot_logoF.png"/></span>
							<span class="foot_logoSpanH">首页</span>
						</a>
					</li>
					<li>
						<a href="javascript:void(0)" class="logoGh">
							<span><img src="/static/wechat/img/foot_logoGh.png"/></span>
							<span class="foot_logoSpan">购物車</span>
						</a>
					</li>
					<li>
						<a href="javascript:void(0)" class="logoRh">
							<span><img src="/static/wechat/img/foot_logoRh.png" /></span>
							<span class="foot_logoSpan">我的</span>
						</a>
					</li>
				</ul>
			</nav>
		</footer>
        <!--错误提示-->
        <div class="cuowu iframe" style="display: none">
            <p class="p1">提示</p>
            <p class="p"><img src="/static/wechat/img/cuowu_tishi.png" style="width: 15%;"/></p>
            <p class="p2">当前药店不在营业时间范围内</p>
            <p class="p3"><a href="#">知道了</a></p>
        </div>
		<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
		<script src="/static/wechat/js/swiper-3.4.2.min.js" type="text/javascript" charset="utf-8"></script>
		<script>
			var mySwiper = new Swiper('.swiper-container', {
				direction: 'horizontal',
				loop: true,
				autoplay: 1000
			})
		</script>
        <script src="https://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
		<script type="text/javascript">
			//获取url参数 分割并赋值
            var param = window.location.search;
            if(param){
                var sp_param = param.split("&");
                var llal = sp_param[0].split("=");
                var llnl = sp_param[1].split("=");
                if(!isNaN(llal[1]) && !isNaN(llnl[1])){
                   var lat = llal[1];
                   var lng = llnl[1];
                }
            }
            $.ajax({
                type: "POST",
                url: "/api/wechat/getSignPackage",
                data:{'lon':lng,'lat':lat},
                dataType: "JSON",
                async: false,
                success: function(data){
                    wx.config({   
                        debug: false,   
                        appId: data.appId,   
                        timestamp:  data.timestamp,   
                        nonceStr:  data.nonceStr,   
                        signature:  data.signature,   
                        jsApiList: [   
                        'checkJsApi',   
                        'getLocation',   
                        ]   
                    });
                    wx.ready(function () {
                        //自动执行的   
                        wx.checkJsApi({        
                            jsApiList: [
                            'getLocation',         
                            ],      
                            success: function (res) { 
                                if (res.checkResult.getLocation == false) {           
                                    alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');           
                                    return;       
                                }         
                            }     
                        });
                        $.post("/api/wechat/get_user_info",function(data){
                            var data = JSON.parse(data);
                            if(data.code == '200'){
                                //如果不支持则不会执行       
                                wx.getLocation({
                                    type:'gcj02',   // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'   
                                    success: function (res) {
                                        var lat = res.latitude;
                                        var lng = res.longitude;
                                        //获取url参数 分割并赋值
                                        var param = window.location.search;
                                        if(param){
                                            var sp_param = param.split("&");
                                            var llal = sp_param[0].split("=");
                                            var llnl = sp_param[1].split("=");
                                            if(!isNaN(llal[1]) && !isNaN(llnl[1])){
                                                lat = llal[1];
                                                lng = llnl[1];
                                            }
                                        }
                                        $.post("/api/wechat/get_location",{'lon':lng,'lat':lat},function(red){
                                            var red = JSON.parse(red);
                                            if(red.data.status == 0){
                                                $('.index_dW').html(red.data.title);
                                                $.post('/api/wechat/pharmacy_datail',function(res){
                                                    var content ='';
                                                    var pharmacy_ismed = '';
                                                    var pharmacy_isdoor = '';
                                                    var discount = '';
                                                    var flag = '';
                                                    var res = JSON.parse(res);
                                                    if(res.code == 200){
                                                        if(res.data.length > 0){
                                                            var var_length = res.data.length;
                                                            $.each(res.data,function(name,value){
                                                                var discount_text = '';
                                                                if(value.pharmacy_ismed == '1'){
                                                                    pharmacy_ismed = '<span class="yiyao1_img"><img src="/static/wechat/img/yiyao1_img.png"/><span>定点医保</span></span>';
                                                                }
                                                                if(value.pharmacy_isdoor == '1'){
                                                                    pharmacy_isdoor = '<span class="yiyao1_img" style="margin-left:10px;"><img src="/static/wechat/img/yaodian_char.png"/><span>送药上门</span></span>';
                                                                }
                                                                if(value.discount > 0){
                                                                    discount_text = '<div class="yiyao1_img1"><span><img src="/static/wechat/img/ze_img.png"/><span>折扣药品'+value.discount+'折起</span></span></div>'
                                                                }
                                                                if (value.flag == 'Y') {
                                                                    flag = 'in';
                                                                }else{
                                                                    flag = 'out';
                                                                }
                                                                if(discount_text){
                                                                    content +=' <div class="fujinyd '+flag+'" data-fid='+value.id+'><div class="fujin_img"><img src='+ value.pharmacy_store_pic +'></div><div class="fujin_yaodian"><div class="fujin_juli"><a href="#">'+ value.pharmacy_name +'</a><span class="yiyao1_img2"><img src="/static/wechat/img/dingwei_img.png"/><span>'+ value.distance_km +'km'+'</span></span></div><div class="fujin_l"><div >'+pharmacy_ismed+pharmacy_isdoor+'</div>'+discount_text+'</div></div></div>';
                                                                }else{
                                                                     content +=' <div class="fujinyd '+flag+'" data-fid='+value.id+'><div class="fujin_img"><img src='+ value.pharmacy_store_pic +'></div><div class="fujin_yaodian"><div class="fujin_juli"><a href="#">'+ value.pharmacy_name +'</a><span class="yiyao1_img2"><img src="/static/wechat/img/dingwei_img.png"/><span>'+ value.distance_km +'km'+'</span></span></div><div class="fujin_l" style="margin-top:15px;"><div >'+pharmacy_ismed+pharmacy_isdoor+'</div>'+discount_text+'</div></div></div>';
                                                                }
                                                                
                                                            });
                                                            $('.list').html(content);
                                                        }
                                                    }
                                                })
                                            }else{
                                                $('#wx_location').html('请选择地址');
                                            }
                                        });    
                                    },
                                    cancel: function (res) {               
                                        //点击后跳转到收获地址栏
                                        location.href='/wechat/index/address_info?to=index';
                                    }       
                                });
                            }else{
                               window.location.href = '/api/wechat/oauth';
                            }
                        })       
                    });
                    wx.error(function (res) { 
                        
                    });
                }
            })
            $('.form_search').on('click',function(){
                location.href='/wechat/index/search';
            })
            $(".index_dW").on('click',function(){
                //点击后跳转到收获地址栏
                location.href='/wechat/index/address_info?to=index';
            })
            $('.logoSpanH').on('click',function(){
                //点击后跳转到收获地址栏
                location.href='/wechat/index/index';
            })
            $('.logoGh').on('click',function(){
                //点击后跳转到购物栏
                location.href='/wechat/Cart/cart';
            })
            $('.logoRh').on('click',function(){
                //点击后跳转到收获地址栏
                location.href='/wechat/index/medicine';
            })
            //点击营业时间内药店栏跳转
            $(document).on('click','.in',function(){
                var f_id = $(this).attr('data-fid');
                location.href='/wechat/pharmacy/index?f_id='+f_id;
            })
            //点击不在营业时间内药店栏提示
            $(document).on('click','.out',function(){
                $('.iframe').toggle();
            })
            $(document).on('click','.p3',function(){
                $('.iframe').toggle();
            })
            
		</script>
	</body>

</html>
