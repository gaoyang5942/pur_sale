<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/usr/local/var/www/pur_sale/public/../app/wechat/view/index/search.html";i:1543988690;}*/ ?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>搜索</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <link rel="stylesheet" type="text/css" href="/static/wechat/css/nav.css" />
        <link rel="stylesheet" type="text/css" href="/static/wechat/css/yaodian_index.css" />
        <link rel="stylesheet" type="text/css" href="/static/wechat/css/index.css" />
    </head>
    <style type="text/css">
        .form_searchD{
            position: fixed;
            top: 0;
            z-index: 333;
            width: 100%;
            background: #EFEFF4;
        }
        .cuowu_div{
            top: 30%;
        }
        #iframe{
            display: none;
        }
        #cuowuTs{
            display: none;
        }
        .search_yp_divHul .search_yp_divImg img{
            height: 50px;
        }
    </style>
    <body>
        <div class="container">
            <div class="container_Div">
                <div class="" style="padding: 1.5rem 0;"></div>
                <form class="form_searchD" action="" method="post">
                    <div class="yaodian_form_search1">
                        <input type="search" placeholder="请输入药品关键字" class="yaodian_index_search" />
                        <img src="/static/wechat/img/sech_img.png" />
                    </div>
                </form>
                <div class="shousuo_div">
                    <div class="shousuo_div1">
                        <span class="shousuo_div_span1 active1">药 品</span>
                        <span class="shousuo_div_span2 hui">药 店</span>
                    </div>
                    <div class="shousuo_div2">
                        <p>已上线药店</p>
                        <div class="shousuo_div3">
                            <a href="javascript:void(0)" class="pharmacy">吉林大药房</a>
                            <a href="javascript:void(0)" class="pharmacy">同春和大药房</a>
                        </div>
                    </div>
                    <div class="shousuo_div4">
                        <p>大家都在搜</p>
                        <div class="shousuo_div3">
                            <a href="javascript:void(0)" class="drugs">感冒</a>
                            <a href="javascript:void(0)" class="drugs">消炎</a>
                            <a href="javascript:void(0)" class="drugs">咳嗽</a>
                        </div>
                    </div>
                </div>
                <!--药品-->
                    <div class="search_yp"></div>
                <!--药品 end-->
                <!--药店-->
                    <div class="fujin"></div>
                <!--药店end-->
                </div>
            </div>
            <!--错误提示-->
            <div class="cuowu iframe cuowu_div" id="iframe">
                <p class="p1">无法操作</p>
                <p class="p"><img src="/static/wechat/img/cuowu_tishi.png" style="width: 15%;"/></p>
                <p class="p2">无法操作</p>
                <p class="p3"><a href="#">知道了</a></p>
            </div>
            <div class="cuowuTs" id="cuowuTs"></div>
            <!--错误提示 end-->
        </div>
        <footer class="footer abs-bottom">
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
    </body>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.post("/api/wechat/get_user_info",function(data){
                var data = JSON.parse(data);
                if(data.code == '202'){
                    window.location.href = '/api/wechat/oauth';
                }
            })
        })
        //错误提示js
        $(".p3").click(function() {
            $("#iframe").toggle();
            $("#cuowuTs").toggle();
        })
        //错误提示js
        $("#cuowuTs").click(function() {
            $("#iframe").hide()
            $("#cuowuTs").hide()
        })
        //药品小tip快捷
        $(document).on('click','.drugs',function(){
            var drug_text = $(this).text();
            $('.yaodian_index_search').val(drug_text);
            search_yp_change(drug_text);
        })
        //药店小 tip 快捷
        $(document).on('click','.pharmacy',function(){
            var pharmacy_text = $(this).text();
            $('.yaodian_index_search').val(pharmacy_text);
            search_yd_change(pharmacy_text);
        })
        //输入完文字 按确定键
        $('.form_searchD').on('submit', function(){
            var active1_text = removeAllSpace($('.active1').text());
            var drug_text = $('.yaodian_index_search').val();
            if(active1_text == '药品'){
                if(drug_text != ''){
                    search_yp_change(drug_text);
                }
            }else if(active1_text == '药店'){
                search_yd_change(drug_text);
            }
            event.preventDefault(); //阻止form表单默认提交
        })
        //搜索  药品 function
        function search_yp_change(drug_text){
            $.post('/api/Search/searchMedicineName',{'drug_detailed_name':drug_text},function(data){
                var data = JSON.parse(data);
                if(data.code == '202'){
                    $(".iframe").show();
                    $(".cuowuTs").show();
                    $('.p2').html('暂无该类药品信息');
                }else{
                    var content = '';
                    var discount_text = '';
                    $.each(data.data,function(e,v){
                        var tat = false;
                        if(v.pah.discount > 0){
                            discount_text = '<p class="search_yp_divName_p"><img src="/static/wechat/img/ze_img.png"/><span>折扣药品<b>'+v.pah.discount+'</b>折起</span></p>';
                        }
                        if(v.pah){
                            content += '<div class="search_yp_div"><div class="search_yp_divH"><ul class="search_yp_divHul"  data-id='+v.pah.id+'><li class="search_yp_divImg"><img src="'+v.pah.pharmacy_store_pic+'"/></li><li class="search_yp_divName"><p class="search_yp_divName_p1"><span class="search_yp_divSpa1">'+v.pah.pharmacy_name+'</span><span class="search_yp_divSpa"><img src="/static/wechat/img/dingwei_img.png"/><span class="search_juli">'+v.pah.distance_km+'km</span></span></p>'+discount_text+'</li></ul></div><div class="search_yp_mian">';
                        }
                        $.each(v.pdt,function(a,b){
                            var img_text = '';
                            if(a <= 1){
                                var d_img = b.drug_detailed_img;
                                var drug_img = d_img.split("admin");
                                if(drug_img[1]){
                                    img_text = '<img src=' +b.drug_detailed_img+ '>';
                                }
                                content += '<div class="search_yp_mianD" data-mid='+b.id+'  data-mdid='+v.pah.id+'><ul><li class="search_yp_mianDli">'+img_text+'</li><li class="search_yp_mianDli1"><p class="yaopin_p">'+b.drug_detailed_name+'</p><p class="search_yp_mianDliP">'+b.drug_detailed_specifications+'</p><p class="search_yp_mianDliP">'+b.drug_detailed_manufactor+'</p><p style="color: red;">￥ <span>'+b.drug_detailed_present_price+'</span></p></li></ul></div></div><div class="clear"></div>';
                            }
                            if(a > 1){
                               tat = true;
                            }
                        })
                        if(tat){
                            content += '<div class="search_chakan"><a href="javascript:void(0)" class="more" data-did='+v.pah.id+' data-drug='+drug_text+'>查看更多 <img src="/static/wechat/img/shouhuo_img.png"/></a></div><div class="clear"></div></div>';
                        }
                    })
                    if(content){
                        $('.search_yp').html(content);
                        $('.search_yp').show();
                        $('.shousuo_div').hide();
                        $("#iframe").hide();
                        $("#cuowuTs").hide();
                    }
                }
            })
        }
        //搜索  药店 function
        function search_yd_change(pharmacy_text){
            $.post('/api/Search/searchMedicine',{'pharmacy_name':pharmacy_text},function(data){
                var data = JSON.parse(data);
                if(data.code == '202'){
                    $(".iframe").show();
                    $(".cuowuTs").show();
                    $('.p2').html('暂无该药店信息');
                }else{
                    var content = '';
                    var pharmacy_ismed = '';
                    var pharmacy_isdoor = '';
                    var discount_text = '';
                    $.each(data.data,function(e,v){
                        var d_img = v.pharmacy_store_pic;
                        var drug_img = d_img.split("admin");
                        if(drug_img[1]){
                            img_text = '<img src=' +v.pharmacy_store_pic+ '>';
                        }
                        if(v.pharmacy_ismed == '1'){
                            pharmacy_ismed = '<span class="yiyao1_img"><img src="/static/wechat/img/yiyao1_img.png" class="yiyao1_Img"/><span>医保定点</span></span>';
                        }
                        if(v.pharmacy_isdoor == '1'){
                            pharmacy_isdoor = '<span class="yiyao1_img"><img src="/static/wechat/img/yaodian_char.png" class="yiyao1_Img"/><span>送药上门</span></span>';
                        }
                        if(v.discount > 0){
                            discount_text = '<div><span class="yiyao1_img1"><img src="/static/wechat/img/ze_img.png" class="yiyao1_Img"/><span>折扣药品'+v.discount+'折起</span></span></div>';
                        }
                        content += '<div class="fujinyd" data-fid='+v.id+'><div class="fujin_img">'+img_text+'</div><div class="fujin_yaodian"><div class="fujin_juli"><a href="javascript:void(0)">'+v.pharmacy_name+'</a><span class="yiyao1_img2"><img src="/static/wechat/img/dingwei_img.png"/><span>'+v.distance_km+'km</span></span></div><div><div>'+pharmacy_ismed+pharmacy_isdoor+'</div>'+discount_text+'</div></div></div>';
                    })
                    $('.fujin').html(content);
                    $('.fujin').show();
                    $('.shousuo_div').hide();
                    $("#iframe").hide();
                    $("#cuowuTs").hide();
                }
            })
        }
        //去掉所有空格
        function removeAllSpace(str) {
            return str.replace(/\s+/g, "");
        }
        //错误提示隐藏
        $('.p3').on('click',function(){
            $(".iframe").hide();
        })
        $('.dizhi_guanli .dizhi_guanli_formA1').each(function() {
            $(this).click(function() {
                $(this).parent().parent().parent().remove('.dizhi_guanli');
            })
        })
        $('.shousuo_div_span1').click(function() {
            $('.shousuo_div4').show()
            $('.shousuo_div2').hide()
        });
        $('.shousuo_div_span2').click(function() {
            $('.shousuo_div2').show()
            $('.shousuo_div4').hide()
        });
        $(".shousuo_div_span1").click(function() {
            $(".shousuo_div_span1").addClass('active1');
            $(".shousuo_div_span1").removeClass('hui');
            $(".shousuo_div_span2").removeClass('active1');
            $(".shousuo_div_span2").addClass('hui');
        })
        $(".shousuo_div_span2").click(function() {
            $(".shousuo_div_span2").addClass('active1');
            $(".shousuo_div_span1").removeClass('active1');
            $(".shousuo_div_span2").removeClass('hui');
            $(".shousuo_div_span1").addClass('hui');
        })
        //大于两个 查看更多按钮显示/隐藏
        $('.search_yp_mian').each(function(e,v){
            if ($(this).find(".search_yp_mianD").length>1) {
                $(this).siblings('.search_chakan').show();
            }else{
                $(this).siblings('.search_chakan').hide();
            }
        })
        $(".yaodian_index_search").keyup(function(){
            if($('.yaodian_index_search').val()!=""){
            $(".shousuo_div").hide()
        }else{
            $(".shousuo_div").show();
            $('.search_yp').hide();
            $('.fujin').hide();
            $("#iframe").hide();
            $("#cuowuTs").hide();
        }
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
        //点击药品标题
        $(document).on('click','.search_yp_divHul',function(){
            var d_id = $(this).attr('data-id');
            location.href='/wechat/pharmacy/index?f_id='+d_id;
        })
        //点击药品
        $(document).on('click','.search_yp_mianD',function(){
            var m_id = $(this).attr('data-mid');//药品id
            var md_id = $(this).attr('data-mdid');//药店id
            location.href='/wechat/drug/index?id='+m_id+'&pid='+md_id;
        })
        //点击查看更多
        $(document).on('click','.more',function(){
            var drug_text = $(this).attr('data-drug');
            var mo_id = $(this).attr('data-did');
            location.href='/wechat/pharmacy/index?mo_id='+mo_id+'&drug_text='+drug_text;
        })
        //点击药店
        $(document).on('click','.fujinyd',function(){
            var f_id = $(this).attr('data-fid');
            location.href='/wechat/pharmacy/index?f_id='+f_id;
        })
    </script>

</html>
