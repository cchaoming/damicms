/* 显示留言 */
function showre(no)
{
var url = "./index.php?m=Guestbook&a=show&page="+no;
$.ajax({
async:false,
type: 'post',
data:'', //客户端小工标志谢绝w3g自动转向
dataType:'xml',
url: url,
success:function(data){
showlist(data);
}
});
}

function showlist(data)
{
	$('#list').html('');
		xmlAuthor = data.getElementsByTagName("Author");
		xmlContent = data.getElementsByTagName("Content");
		xmlNoI = data.getElementsByTagName("NoI");
		xmlreContent = data.getElementsByTagName("reContent");
		//输出
		for (i=0;i<xmlContent.length;i++) {  
		    var Content = xmlContent[i].firstChild.data;            
			if (Content == "没有留言") {
			    //alert("1111");
			    var str = "<li style='padding:5px;color:#ff0000'>暂时还没有留言</li>";
			}
			else {
			    var Author = xmlAuthor[i].firstChild.data;
			    var PostTime = xmlAuthor[i].getAttribute('PostTime');
			    var ID = xmlNoI[i].firstChild.data;
				//alert(ID);
				var reContent = xmlreContent[i].firstChild.data;
			   var str = "<li ><h3>留言时间:"+PostTime+" 留言者：<font color='red'>"+Author+"</font></h3><p>"+Content+"</p><p class='reply'>"+reContent+"</p></li>"
			}
            $("#list").append(str);
		}
		//重新渲染UL输出
		$("#list").listview("refresh");
		 //输出分页信息
		P_Nums = parseInt(data.getElementsByTagName("data")[0].getAttribute('P_Nums'));
		var A_Nums = data.getElementsByTagName("data")[0].getAttribute('A_Nums');
		var aaa="";
		document.getElementById("pinglunym").innerHTML = aaa;
		if (P_Nums>1) {
	    	var page = parseInt(data.getElementsByTagName("data")[0].getAttribute('page'));
	    	var ID = data.getElementsByTagName("data")[0].getAttribute('ID');
			var D_Nums = data.getElementsByTagName("data")[0].getAttribute('D_Nums');
		    var l1 = "<div style='margin:30px 0 20px 0;'>&nbsp;共"+A_Nums+"条"
			var last_p = page - 1;
			l1 = (page>1)?l1+"<a href='javascript:showre(1)' >首页</a> <a href='javascript:showre("+ last_p + ")'>上一页</a>":l1;
			var l2 = "";		
			var next_p = page + 1;
			l2 =  (page < P_Nums)?l2+" <a href='javascript:showre("+ next_p + ")'>下一页</a> <a href='javascript:showre("+P_Nums+")'>尾页</a></div>":l2;
			document.getElementById("MultiPage").innerHTML = l1+l2;
		}
	
	
}
/* 发送留言 */
function AddNew() {
var Author = escape($("#Author").val());
    var Content = escape($("#plContent").val());
	var tel =     escape($("#tel").val());
	var precontent=$("#plContent").val();
	if (Author == "" || Content == "") {
	    alert("请填写完整！");
		return false;
	}
	if(precontent.length <5){
	alert("留言内容最少5字,请调整后再发布!");
	return false;
	}
	
var url = "./index.php?m=Guestbook&a=update";
var s_data = "tel="+tel+"&author="+Author+"&content="+Content;
$.ajax({
async:false,
type: 'post',
data:s_data, //客户端小工标志谢绝w3g自动转向
dataType:'html',
url: url,
success:function(data){
location.href = './index.php?m=Guestbook&a=index';
}
}); 
} 