function do_delete(uids) {
	if(!(uids instanceof Array)) {
		uids = [uids];
	}
	var data = {uids: uids.join(';')};
	$.post('?_a=shop&_u=api.delcat', data, function(ret){
		console.log(ret);
		window.location.reload();
	});
}

$('.cdelete').click(function(){
	var uid = $(this).attr('data-id');
	if(!confirm('确定要删除吗?')) {
		return;
	}
	do_delete(uid);
});

$('.cstatus').click(function(){
	var uid = $(this).parent().parent().attr('data-id');
	var status = $(this).attr('data-status');
	console.log(uid, status);
	var data = {uid:uid, status:1-status};
	$.post('?_a=shop&_u=api.addcat', data, function(ret){
		console.log(ret);
		window.location.reload();
	});
	
});

$('.ccheckall').click(function(){
	var checked = $(this).prop('checked');
	$('.ccheck').prop('checked', checked);
});

$('.cdeleteall').click(function(){
	var uids = [];
	$('.ccheck').each(function(){
		if ($(this).prop('checked')) {
			uids.push($(this).parent().parent().attr('data-id'));
		}
	});
	console.log(uids);
	if(!uids.length) {
		alert('请选择项目!');return;
	}
	if(!confirm('确定要删除吗?')) {
		return;
	}
    console.log(uids)
	do_delete(uids);
});

/*
	amazeui 会调用一次change事件,此时不刷新
*/
var by_amaze_init = 1;
$('.option_cat').change(function(){
	if(by_amaze_init) {
		by_amaze_init = 0;
		return;
	}

	var cat = $(this).val();
	window.location.href='?_a=shop&_u=sp.catlist&parent_uid=' + cat;
});


/*yhc*/

$(document).ready(function () {
    /*分类选择弹出框*/
    $(".choose-cats").click(function () {
        $('#chooseCats').modal({
            closeViaDimmer:false,
            width:1000
        })
    });
    /*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    /*组成数据listData过程*/
    /*排序*/
    if(!catsAll || typeof catsAll!="object"){
        return
    }
    
    catsAll.sort(function (a,b) {
        return (parseInt(a.parent_uid)-parseInt(b.parent_uid))
    });

    var listData = [];

    //data:试匹配的数组，obj：需要匹配的对象
    function loopListData(data,obj){
        var puid = obj.parent_uid;
        var uid = obj.uid;
        if(data[puid]){
            //匹配到的话
            if(!data[puid]["child"]) data[puid]["child"]=[];    //判断有没有，没有就设定为数组
            data[puid]["child"][uid]={name:obj.title,uid:obj.uid,puid:obj.parent_uid};
            return true;                //匹配成功返回真
        }else{
            var status_success = false;
            //匹配不成功的话
            $.each(data, function () {
                if(this.child){
                    var now_status = loopListData(this.child,obj);   //往下一层匹配,记录状态
                    if(!status_success){                            //要判断状态是否已经在前面记录了
                        status_success=now_status
                    }
                }
            });
            return status_success
        }
    }
    /*！！自检测自循环功能！！！！！！！！！！*/
    function checkBad(cats,time){
        if(!time) time=0;   //默认值
        var badCats = [];
        $.each(cats, function () {
            if(this.parent_cat){
                var status = loopListData(listData,this);
                if(!status){
                    badCats.push(this);         //挑出坏的
                }
            }else{
                listData[this.uid]={name:this.title,uid:this.uid,puid:this.parent_uid};
            }
        });

        if((badCats.length==0)||(time>5)){
            console.log("不继续了",badCats.length,time);
            return badCats
        }else{
            console.log("继续");
            time++;
            return checkBad(badCats,time);  //将没有的一级级往上传
        }
    }

    var noCats = checkBad(catsAll);
    //没有分类
    if(!(noCats.length==0)){
        var html='<span>其他分类：</span>';
        $.each(noCats, function () {
            html+='<section onclick="window.location.href=\'?_a=shop&_u=sp.addcat&uid='+this.uid+'\'">'+this.title+'</section>';
        });
        $(".noCats-box").append(html)
    }

    var catListData = {
        btnCss:{
            marginLeft:"1em"
        },
        firstTitleCss:{
          display:"none"
        },
        data: listData,
        func:{
            click: function () {
                var parent = $(this).parent();
                if(parent.hasClass("cly-open-btn")){
                    $(this).text("选择分类").parent().find("ul").toggle();
                    $(".noCats-box").toggle()
                }else{
                    var uid = $(this).data("uid");
                    var puid = $(this).data("puid");
                    console.log(uid,puid,$(this).siblings("ul").length);
                    if(typeof puid=="undefined"){                       //没有父级就是顶级，所以返回首页
                        window.location.href='/?_a=shop&_u=sp.catlist';
                    //}else if(!($(this).siblings("ul").length==0)){      //有下一级的话，就返回他的下级列表
                    //    window.location.href='/?_a=shop&_u=sp.catlist&parent_uid='+uid
                    }else{                                              //有父级，没有下一级，就返回他的父级
                        window.location.href='/?_a=shop&_u=sp.catlist&parent_uid='+puid
                    }
                }
            },
            mouseenter: function () {
                $(this).siblings("ul").css("background","#D3D3D3")
            },
            mouseleave: function () {
                $(this).siblings("ul").css("background","#eee")
            }
        }
    };

    if(typeof cats=="object"&&(!(cats==null))){
        if(cats[0].parent_cat){
            catListData["btnWord"] = cats[0].parent_cat['title']
        }
    }

    // $(".catList-yhc").catList(catListData);

});


$('.option_key').keydown(function(e){
    if(e.keyCode == 13) {
        // $('.option_key_btn').click();
        var key = $('.option_key').val();

        console.log("key >>>", key);
        //允许关键字为空，表示清空条件
        if(1 || key) {
            window.location.href='?_easy=shop.sp.catlist'+'&key='+key;
        }
    }
});