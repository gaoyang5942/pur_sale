 function get_user_info(){
        $.post("/wechat/index/get_user_info",{},function(data){
            var data = JSON.parse(data);
            if(data.code == '200'){
                alert(data);
                return data;
            } else{
                return 2;
            }
        })
    }

    function get_location(){
        wx.getLocation({            
        success: function (res) {
        //alert(res);
        var res = JSON.stringify(res);
        return res;
        },
        cancel: function (res) {                
            return 2;           
        }       
        });
    }
