var weixin_menu_table;
var menuTemplateRecords;

$(document).ready(function(){
	weixin_menu_table = $('#editPWXCMTTable').dataTable({
        "aoColumns": [
  			{"bSortable": false },
  			{"bSortable": false },
  			{"bSortable": false },
  			{"bSortable": false ,"sClass":"center"},
  			{"bSortable": false , "bVisible": false},
  			{"bSortable": false , "bVisible": false},
			{"bSortable": false , "bVisible": false},
		    {"bSortable": true , "bVisible": false}],
		"aaSorting": [[ 7, "asc" ]],    
        "bAutoWidth": false,
        "bPaginate": 1,
		"iDisplayLength": 25,
        "bScrollCollapse": true,
		"oLanguage": {
		    "oPaginate": {
		    	"sFirst": "首页",
		    	"sLast": "末页",
		    	"sNext": "下一页",
		    	"sPrevious": "上一页"
		          },
		    "sEmptyTable": "没有可显示的数据&nbsp;",
		    "sInfo": "显示&nbsp;&nbsp;_START_&nbsp;&nbsp;到&nbsp;&nbsp;_END_&nbsp;&nbsp;条记录，共有&nbsp;&nbsp;_TOTAL_&nbsp;&nbsp;条记录",
		    "sInfoEmpty": "没有记录",
		    "sInfoFiltered": "(从当前&nbsp;&nbsp; _MAX_&nbsp;&nbsp; 条记录中搜索)",
		    "sLengthMenu": "每页显示记录数:&nbsp;&nbsp;_MENU_",
		    "sLoadingRecords": "正在加载数据中...",
		    "sProcessing": "正在处理中...",
		    "sSearch": "按关键词或标题搜索&nbsp;&nbsp;",
		    "sZeroRecords": "没有找到匹配的记录&nbsp;"
		 }
    });
initWeixinMenu();
});



function ifContainSubRecord(index,array){
	var contain = false;
	for(var i=0;i<array.length;i++){
		if(array[i].parentId==index){
			contain = true;
			break;
		}
	}
	return contain;
}

function checkSubRecordCountInDataSource(index){
	var count = 0 ;
	for(var i =0; i<menuTemplateRecords.length;i++){
		if(menuTemplateRecords[i].parentId==index){
			count++;
		}
	}
	return count;
}

//增加 子记录
function addSubRecord(index){
	var backupRecords = weixin_menu_table._("tr");

	//主菜单没有删除 和键值
	var tableIndex = getIndexInArray(index,backupRecords);
	var record = getRecordByIndex(index,backupRecords);
	if(checkChildCount(index,backupRecords)==4){
		record[1]= "";//当增加第五条记录时 隐藏添加按钮 
	}
	record[2]= "";//没有key值
	record[3]= "";//不能直接删除
	backupRecords.splice(tableIndex,1,record);
	
	var subRecord = formatSubRecord(index);
	var lastSubRecordIndex = tableIndex+ checkChildCount(index,backupRecords);
	backupRecords.splice(lastSubRecordIndex+1,0,subRecord);
	
	//插入记录到data source
	var dateRecord = {
		"index":subRecord[6],
		"level":subRecord[5],
		"name":"",
		"key":"",
		"parentId":subRecord[4],
		"enable":'0_' + subRecord[4] + '_' + subRecord[6]
	};
	menuTemplateRecords.splice(lastSubRecordIndex+1,0,dateRecord);
	
	weixin_menu_table.fnClearTable();
	addDataToTable(backupRecords);
	setDataToInputFiled();
	$('#editPWXCMTTable tbody td').removeClass('sorting_1');
}

function deleteMenuItem(index){
	var backupRecords = weixin_menu_table._("tr");
	if(backupRecords.length==2){
		alert("一级菜单项个数至少要保留两个！");
		return;
	}
	//删除当前菜单
	var tableIndex = getIndexInArray(index,backupRecords);
	var record  = getRecordByIndex(index,backupRecords);
	//alert(record[6]);
	//ajax提交删除
	$.post(del_url,{id:record[6]},function(result){});
	backupRecords.splice(tableIndex,1);
	//从datasource里面删除记录
	menuTemplateRecords.splice(tableIndex,1);
	
	
	//如果 删除了最后一个子菜单的处理
	var parentRecord = getParentRecord(record[4],backupRecords);
	
	if(parentRecord!=null){
		
		if(checkChildCount(parentRecord[6],backupRecords)==4){
			parentRecord[1]= '<a href="javascript:addSubRecord('+parentRecord[6]+')"><img style="vertical-align: middle;" src="/Public/Admin/images/icon/addsubmenu.png"/><span style="vertical-align: middle;" >添加子菜单<span></a>';//当删除第五条记录时 显示添加按钮 
		}
		if(checkChildCount(parentRecord[6],backupRecords)==0){
			parentRecord[2] = keyHtml = '<input type="text" name="value_' +parentRecord[4] + '_' + parentRecord[6] + '" id="key_'+parentRecord[6]+'" class="w280" value="" onchange="changeValue(this)"/><select name="type_' +parentRecord[4] + '_' + parentRecord[6] + '" id="type_'+parentRecord[6]+'"><option value="0">网页链接</option><option value="1">按关键字推送微信图文</option></select>';
			parentRecord[3] =  '<a href="javascript:void(0);" onclick="deleteMenuItem('+parentRecord[6]+')">删除</a>';
		}

		var parentTableIndex = getIndexInArray(parentRecord[6],backupRecords);
		backupRecords.splice(parentTableIndex,1,parentRecord);
	}
	
	weixin_menu_table.fnClearTable();
	addDataToTable(backupRecords);
	setDataToInputFiled();
	$('#editPWXCMTTable tbody td').removeClass('sorting_1');
}

function getParentRecord(parentId , backupRecords){	
	var records = backupRecords;
	var record = null;
	var length = records.length;
	for(var i=0;i<length;i++){
		if(parseInt(records[i][6])==parentId){
			record = records[i];
			break;
		}
	}
	
	return record;
}

function getIndexInArray(index,backupRecords){
	var records = backupRecords;
	var tableIndex = 0 ;
	var length = records.length;
	for(var i=0;i<length;i++){
		if(parseInt(records[i][6])==index){
			tableIndex = i;
			break;
		}
	}
	return tableIndex;
}

function getRecordByIndex(index,backupRecords){
	var records = backupRecords;
	var record = null;
	var length = records.length;
	for(var i=0;i<length;i++){
		if(parseInt(records[i][6])==index){
			record = records[i];
			break;
		}
	}
	
	return record;
}

//格式化子记录
function formatSubRecord(index){
	var subRecord = [];
	var i = getMaxIndex();
	subRecord[0] = '<div ><img src="/Public/Admin/images/icon/submenu-branch.png" style="width:10px; float:left;"><input type="text" name="subkey_' + index + '_' + i + '" id="name_'+i+'" style="float:left;width:158px;pedding-left:20px;" onchange="changeValue(this)"/></div>';
	subRecord[1] = '';
	subRecord[2] = '<input type="text" class="w280" name="value_' + index + '_' + i + '" id="key_'+i+'" onchange="changeValue(this)"/><select name="type_' + index + '_' + i + '" id="type_'+i+'" onchange="changeValue(this)"><option value="0" selected="selected">网页链接</option><option value="1">按关键字推送微信图文</option></select>';
	subRecord[3] = '<a href="javascript:void(0);" onclick="deleteMenuItem('+i+')">删除</a>';
	subRecord[4] = index; //parent
	subRecord[5] = 1; //level
	subRecord[6] = i; //id
	subRecord[7] = '0_' + index+ '_' + i;// 排序
	
	return subRecord;
	
}

function getMaxIndex(){
	var maxIndex = 0;
	if(menuTemplateRecords.length>0){
		maxIndex = parseInt(menuTemplateRecords[0].index);
	}
	for(var i=1;i< menuTemplateRecords.length;i++){
		if(maxIndex<parseInt(menuTemplateRecords[i].index)){
			maxIndex = parseInt(menuTemplateRecords[i].index);
		}
	}
	return maxIndex + 1;
}

function checkChildCount(index,records){
	var count = 0;
	for(var i=0;i<records.length;i++){
		if(parseInt(records[i][4])==index){//perantId = index
			count++;
		}
	}
	return count;
}

function addDataToTable(backupRecords){
	for(var j=0;j< backupRecords.length;j++){
		weixin_menu_table.fnAddData([
			backupRecords[j][0],
			backupRecords[j][1],
			backupRecords[j][2],
			backupRecords[j][3],
			backupRecords[j][4],
			backupRecords[j][5],
			backupRecords[j][6],
			backupRecords[j][7]
		],true);
	}
}

function setDataToInputFiled(){
	for(var i=0;i<menuTemplateRecords.length;i++){
		$("#name_"+menuTemplateRecords[i].index).val(menuTemplateRecords[i].name);
		$("#key_"+menuTemplateRecords[i].index).val(menuTemplateRecords[i].key);
		if( typeof(menuTemplateRecords[i].type) == "undefined"){var type = 0;}else{var type = menuTemplateRecords[i].type;}
		$("#type_"+menuTemplateRecords[i].index).val(type); //菜单类型
		$("#checkBox_"+menuTemplateRecords[i].index).attr("checked",menuTemplateRecords[i].enable);
	}
}

//每当页面input field改变值
function changeValue(object){
	var id = object.id;
	var prefix = id.split("_")[0];
	var index = id.split("_")[1];
	
	for(var i =0; i< menuTemplateRecords.length;i++){
		if(menuTemplateRecords[i].index==index){
			if(prefix!="checkBox"){
				//alert($(object).val());
				menuTemplateRecords[i][prefix]= $(object).val();
			}else{
				menuTemplateRecords[i]["enable"] = object.checked;
			}
			
		}
	}
}

function containLimitLevelRecord(){
	var count = 0 ;
	for(var i=0 ; i<menuTemplateRecords.length;i++){
		if (menuTemplateRecords[i].level==0){
			count++;
		}
	}
	if(count==3){
		return true;
	}else{
		return false;
	}
}