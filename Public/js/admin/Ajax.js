$(function(){
    //////////////////////////ajax请求加载//////////////////////
    $(document).ajaxStart(function(){
        $(".loadajax").show();
    }).ajaxStop(function(){
        $(".loadajax").hide();
    })
})
var Ajax = {
    isJson: function (obj) {
        var isjson = typeof (obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
        return isjson;
    },
    isEmptyValueJson: function (json) {
        i = 0;
        for (val in json) {
            if (json[val] != "")
                i++;
        }
        if (i == 0)
            return true;
        return false;
    },
    isFunction: function (funcName) {
        //是否存在指定函数
        try {
            if (typeof (funcName) === "function") {
                return true;
            }
        } catch (e) {
        }
        return false;

    },
    request: function (eventFrom, event, url) {
        if (!eventFrom || eventFrom == undefined) {
            $.Showmsg("缺少事件来源对象");
            setTimeout(function () {
                $.Hidemsg()
            }, 2000);
            return false;
        }
        if (!event || event == undefined) {
            $.Showmsg("缺少事件类型");
            setTimeout(function () {
                $.Hidemsg()
            }, 2000);
            return false;
        }
        var form = $(eventFrom).closest("form").Validform({tiptype: 1, ajaxPost: true, callback: function (data) {
                $.Showmsg(data.info);
                setTimeout(function () {
                    $("input[type=button].buttons-c").removeClass("disabled").removeAttr("disabled");
                    $.Hidemsg();
                    if (data.status == "y" || parseInt(data.status) == 1) {
                        if (Ajax.isFunction(url)) {
                            url(data);
                        } else {
                            if (url && url != undefined) {
                                if (url.indexOf("/") === 0) {
                                    window.location.href = url;
                                } else {
                                    //假定为js代码
                                    eval(url);
                                }
                            } else if (data.url && data.url != undefined) {
                                window.location.href = data.url;
                            }
                        }
                    }
                }, 2000);
            }
        });
        $(".global").on(event, eventFrom, function () {
            $("input[type=button].buttons-c").addClass("disabled").attr("disabled", true);
            form.ajaxPost();
        })
    },
    simpleRequest: function (eventFrom, event, url, _success, data, isnull) {
        if (isnull == true && (data == {} || data == "" || Ajax.isEmptyValueJson(data)))
            return false;
        if (!eventFrom || eventFrom == undefined) {
            $.Showmsg("缺少事件来源对象");
            setTimeout(function () {
                $.Hidemsg()
            }, 2000);
            return false;
        }
        if (!event || event == undefined) {
            $.Showmsg("缺少事件类型");
            setTimeout(function () {
                $.Hidemsg()
            }, 2000);
            return false;
        }
        if (data == undefined || ("object" != typeof (data) && "string" != typeof (data))) {
            data = {};
        }
        $(document).on(event, eventFrom, function () {
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data,
                success: _success,
                error: function (jqXHR, textCode, jqThrow) {
                    if (textCode == "timeout") {
                        $.Showmsg("请求超时");
                    } else {
                        $.Showmsg("请求错误");
                    }
                    setTimeout(function () {
                        $.Hidemsg()
                    }, 2000);
                }
            })
        })
    },
    requestWithoutevent: function (url, _success, data, isnull, json) {
        if (isnull == true && (data == {} || data == "" || Ajax.isEmptyValueJson(data)))
            return false;
        if (data == undefined || ("object" != typeof (data) && "string" != typeof (data))) {
            data = {};
        }
        if (json == undefined || !json)
            json = "json";
        $.ajax({
            url: url,
            type: "POST",
            dataType: json,
            data: data,
            success: _success,
            error: function (jqXHR, textCode, jqThrow) {
                if (textCode == "timeout") {
                    $.Showmsg("请求超时");
                } else {
                    $.Showmsg("请求错误");
                }
                setTimeout(function () {
                    $.Hidemsg()
                }, 2000);
            }
        })
    },
    ////////////////////////////局部刷新页面////////////////////////////////////
    //url支持子元素，与地址用空格隔开
    reloadpage: function (url, placeholder, data, meihuaform, fromEle, callback) {
        if (fromEle) {
            var ev = fromEle[0];
            var ele = fromEle[1];
            if (!ev || !ele)
                return false;
            $(document).on(ev, ele, function () {
                $(placeholder).load(url, data, function () {
                    if (meihuaform === "true") {
                        App.uniform();
                    } else if (callback) {
                        eval(callback(meihuaform));
                    }
                });
            })
        } else {
            $(placeholder).load(url, data, function (response) {
                if (meihuaform === "true") {
                    App.uniform();
                } else if (callback) {
                    eval(callback(meihuaform,response));
                }
            });
        }
    },
    reloadpage2: function (el) {
        var data = $("form[name=forms]").serializeArray();
        if(document.getElementById("sample_1")){
            $("#sample_1 tbody").load(urls + " #sample_1 tbody tr", data, function () {
                App.uniform();
                App.unblockUI(el);
            })
        }else if($(".tabBody_text").size()>0){
            var tabBody_text = $(".tabBody_text").is(":visible");
            $(".tabBody_text").load(urls + " .tabBody_text ul ", data, function () {
                if(tabBody_text){
                    $(".tabBody .tabBody_text").show();
                    $(".tabBody .tabBody_image").hide();
                }else{
                    $(".tabBody .tabBody_image").show();
                    $(".tabBody .tabBody_text").hide();
                }
                App.uniform();
                App.unblockUI(el);
            })
        }
    },
    
    page:function(url,condition,contain,pager,callback,meihua){
        if(!url)url = urls;
        if(Ajax.isFunction(callback)){
            Ajax.reloadpage(url+" "+pager,contain,condition,meihua,null,callback);
            $(".BIallBillBox").height($(".CBRightBox").height() - 162);
            $('.BIallBillBox').perfectScrollbar();
        }else{
            Ajax.reloadpage(url+" "+pager,contain,condition,meihua);
        }
    }
    
}

