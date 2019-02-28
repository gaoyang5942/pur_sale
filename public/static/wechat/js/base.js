/**
 * created by yongshi
 */

/**
 * 获取url中参数
 * @return {array} 由url中参数组成的对象
 */
function get_url_param()
{
	var param = location.search;//获取url中"?"符以及后面的字符串
	var param_obj = new Object();

	if(param.indexOf('?') !== -1)
	{
		var str_arr = param.substr(1).split('&');
		for(var param_content of str_arr)
		{
			var param_arr = param_content.split('=');
			param_obj[param_arr[0]] = param_arr[1];
		}
	}

	return param_obj;
}

/**
 * 根据订单状态码获取订单状态
 * @param  {int} code 订单状态码
 * @return {string}   订单状态
 */
function get_order_status(code)
{
	var code_status = {
		0: '下单成功',
		1: '完成备药',
		2: '完成取药',
		3: '配送中',
		4: '送达',
		5: '等待付款',
		6: '付款完成',
		7: '退款中',
		8: '退款完成',
		9: '订单取消',
		10:'订单完成',
	};

	return typeof code_status[code] == 'undefined' ? '' : code_status[code];
}

/**
 * 根据订单状态取回按钮动作信息
 * @param  {int} pay_type 支付方式
 * @param  {int} code     订单状态码
 * @return {array}        按钮动作信息
 */
function get_action_url(pay_type, code, order_no)
{
	var code_action;

	if(pay_type == 1)
	{
		code_action = {
			0: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['去付款', '/wechat/weins/weinspay?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			1: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			2: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			3: [['', '', ''], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			// 3: [['退款', 'refund_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			4: [['查看详情', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd1'], ['订单完成', '', 'dingda_Dfk_foot_dd']],
			5: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['去付款', '/wechat/weins/weinspay?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			6: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			7: [['删除订单', 'delete_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			8: [['删除订单', 'delete_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			9: [['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			10: [['', '', 'dingda_Dfk_foot_dd']]
		};
	}
	else if(pay_type == 2)
	{
		code_action = {
			0: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			1: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			2: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			3: [['', '', ''], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			// 3: [['退款', 'refund_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			4: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['去付款', '/wechat/weins/weinspay?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			5: [['取消订单', 'cancel_order', 'dingda_Dfk_foot_dd1'], ['去付款', '/wechat/weins/weinspay?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			6: [['查看详情', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd1'], ['订单完成', '', 'dingda_Dfk_foot_dd']],
			7: [['删除订单', 'delete_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			8: [['删除订单', 'delete_order', 'dingda_Dfk_foot_dd1'], ['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			9: [['查看订单', '/wechat/order/detail?order_no=' + order_no, 'dingda_Dfk_foot_dd']],
			10: [['', '', 'dingda_Dfk_foot_dd']]
		};
	}

	return typeof code_action[code] == 'undefined' ? [] : code_action[code];
}

/**
 * 取消订单
 * @param  {string} order_no 订单编号
 */
function cancel_order(order_no)
{
	if(confirm('确认取消订单吗？'))
	{
		$.post('/api/order/get_status',{order_no: order_no},function(data){
			if(data.code == 200){
				if(data.status == 6){
					$.post('/api/weins/refund',{order_no: order_no},function(otg){
						if(otg.code == 200){
							window.location.reload();
							return false;
						}
					})
				}else{
					$.post('/api/order/cancel_order',{order_no: order_no},function(res){
						if(res.code == 200){
							window.location.reload()
							return false;
						}
					})
				}
			}else{
				alert('获取订单状态失败，稍后重试');
				return false;
			}
		})
	}
}

/**
 * 删除订单
 * @param  {string} order_no 订单编号
 */
function delete_order(order_no)
{
	if(confirm('确认删除订单吗？'))
	{
		$.ajax({
			type: 'post',
			url: '/api/order/delete_order',
			data: {order_no: order_no},
			dataType: 'json',
			success: function(response){
				if(response.code == 200)
				{
					location.reload();
				}
			},
			error: function(error){
				
			}
		});
	}
}

function refund_order(order_no)
{
	if(confirm('确认申请退款吗？'))
	{
		$.ajax({
			type: 'post',
			url: '/api/order/refund_order',
			data: {order_no: order_no},
			dataType: 'json',
			success: function(response){
				if(response.code == 200)
				{
					alert('申请退款成功');
					location.reload();
				}
				else
				{
					alert('申请退款失败');
					location.reload();
				}
			},
			error: function(error){
				console.log(error);
				alert('申请退款失败');
				location.reload();
			}
		});
	}
}

/**
 * 生成订单列表HTML
 * @param  {array} order_info 订单信息
 * @return {string}           订单列表HTML
 */
function order_list(order_info)
{
	var action_list = ['cancel_order', 'delete_order', 'refund_order'];//订单列表页按钮动作
	var order_html = '';

	// <div class="ywcheng">
	// 	<div class="ywcheng_h">
	// 		<a href="#" class="ywcheng_h_a">
	// 			<img src="__STATIC__/wechat/img/yaodian_img.png" />
	// 			<span>吉林大药房 </span>
	// 		</a>
	// 		<span class="yiwanc">待付款</span>
	// 	</div>
	// 	<div class="ywcheng_mian">
	// 		<div class="ywcheng_mian_sp">
	// 			<ul class="ywcheng_mian_sp_ul1">
	// 				<li class="ywcheng_mian_ulImg"><img src="__STATIC__/wechat/img/ef0591ef1e7e8d5f1ffbc98ba859d59.png" /></li>
	// 				<li class="ywcheng_mian_ulText">
	// 					<p class="ywcheng_mian_p1">藿香正气水</p>
	// 					<p>该药品规格</p>
	// 					<p class="dingdanDfh_mian_p">
	// 						<span>￥12.00</span>
	// 						<span class="sancu_span"><del>￥22.00</del></span>
	// 						<span class="ywcheng_mian_num">x1</span>
	// 					</p>
	// 				</li>
	// 			</ul>

	// 		</div><br>
	// 		<div class="ywcheng_mian_jg">
	// 			<p>
	// 				共计1件商品&nbsp;&nbsp;&nbsp;&nbsp; 合计 : <span style="color: #000;">￥12.00</span>
	// 			</p>

	// 		</div>
	// 	</div>
	// 	<div class="ywcheng_foot">
	// 		<div class="ywcheng_foot_div">
	// 			<span>超过<span>60s</span>，订单取消</span>
	// 			<a href="#" class="dingda_Dfk_foot_dd">去付款</a>
	// 		</div>

	// 	</div>
	// 	<div class="clear"></div>
	// </div>

	for(var order of order_info)//循环订单
	{
		order_html += '<div class="ywcheng">';
		order_html += '<div class="ywcheng_h">';
		order_html += '<a href="/wechat/Pharmacy?f_id='+order.pharmacy.id+'" class="ywcheng_h_a">';
		order_html += '<img src="'+static+'/wechat/img/yaodian_img.png" />';
		order_html += '<span>'+order.pharmacy.pharmacy_name+'</span>';
		order_html += '</a>';
		order_html += '<span class="yiwanc">'+get_order_status(order.drugorder_status)+'</span>';
		order_html += '</div>';
		order_html += '<div class="ywcheng_mian">';
		order_html += '<div class="ywcheng_mian_sp">';
		var count = 0;
		for(var drug of order.orderproducts)//循环药品
		{
			order_html += '<ul class="ywcheng_mian_sp_ul1">';
			order_html += '<li class="ywcheng_mian_ulImg">';
			if(drug.detail.drug_detailed_img)
			{
				order_html += '<img src="'+upload+'/'+drug.detail.drug_detailed_img+'" />';
			}
			else
			{
				order_html += '<img src="'+static+'/wechat/img/none.jpg" />';
			}
			order_html += '</li>';
			order_html += '<li class="ywcheng_mian_ulText">';
			order_html += '<p class="ywcheng_mian_p1">'+drug.detail.drug_detailed_name+'</p>';
			order_html += '<p>'+drug.detail.drug_detailed_specifications+'</p>';
			order_html += '<p class="dingdanDfh_mian_p">';
			order_html += '<span>￥'+drug.drugstore_pre_price/100+'</span>';
			order_html += '<span class="ywcheng_mian_num">x'+drug.drugstore_num+'</span>';
			order_html += '</p>';
			order_html += '</li>';
			order_html += '</ul>';
			count += parseInt(drug.drugstore_num);
		}
		order_html += '</div><br>';
		order_html += '<div class="ywcheng_mian_jg">';
		order_html += '<p>共计'+count+'件商品&nbsp;&nbsp;&nbsp;&nbsp; 合计 : <span style="color: #000;">￥'+order.drugorder_amount_payment/100+'</span></p>';
		order_html += '</div>';
		order_html += '</div>';
		order_html += '<div class="ywcheng_foot">';
		order_html += '<div class="ywcheng_foot_div">';
		// alert(order.drugorder_payment_method+'|'+order.drugorder_status+'|'+order.drugorder_number);
		var action_url = get_action_url(order.drugorder_payment_method, order.drugorder_status, order.id);
		for(var action of action_url)
		{
			if($.inArray(action[1], action_list) !== -1)
			{
				if (action[1] != '') {
					var urls = 'onclick="'+action[1]+'(\''+order.drugorder_number+'\')"';
				}else{
					var urls = '';
				}
				if (action[0] != '') {
					order_html += '<a class="'+action[2]+'"'+urls+'href="javascript:void(0)" class="dingda_Dfk_foot_dd">'+action[0]+'</a>';
				}else{
					if (order.drugorder_status == 10) {
						// order_html += "<a href='/wechat/order/detail?order_no="+order.id+"' class='dingda_Dfk_foot_dd1'>查看订单</a>";
					}
				}
			}
			else
			{
				if (action[1] != '') {
					var urls = 'href="'+action[1]+'"';
				}else{
					var urls = '';
				}
				if (action[0] != '') {
					order_html += '<a class="'+action[2]+'"'+urls+'class="dingda_Dfk_foot_dd">'+action[0]+'</a>';
					if (order.drugorder_status == 5) {
						order_html += "<a href='/wechat/order/detail?order_no="+order.id+"' class='dingda_Dfk_foot_dd1'>查看订单</a>";
					}
				}else{
					if (order.drugorder_status == 10) {
						order_html += "<a href='/wechat/order/detail?order_no="+order.id+"' class='dingda_Dfk_foot_dd1'>查看订单</a>";
					}

				}
			}
		}
		order_html += '</div>';
		order_html += '</div>';
		order_html += '<div class="clear"></div>';
		order_html += '</div>';
	}

	return order_html;
}
