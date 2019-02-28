function verifydrug(cart){
	$.post('/api/Drug/verifydrug', {'carts' : cart}, function(result){
		if (result.code == 400) {
			$("#tab").hide();
			var count = $('#counts').html();
			if (count > 0) {
				if (result.msg == 1) {
					$('#postsale').hide();
					$('#btn2').attr('checked','checked');
				}else{
					$('#btn1').attr('checked','checked');
				}
				$("#tab_quyao").show();
				$("#cover1").show();
				$('#tab1').show();
			}
		}else if(result.code == 300) {
			$('#kucun').html(result.msg);
			$('#iframes').show();
		}else{
			var html = '';
			for(var i in result.msg){
				html += result.msg[i] + '</br>';
			}
			$('#drugs').html(html);
			$('.xiajia').show();
		}
	});
}