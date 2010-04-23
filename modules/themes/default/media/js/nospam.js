// 获取预处理代码的 ID
var check_field = 'random';
var nospam_id = 'nospam';
var nospam = document.getElementById(nospam_id);
// 定义数组
window.__onclick = [];

// 如果 nospam 不为 null 且  nospam 的标签不是 FROM
while(nospam != null && nospam.tagName != 'FORM'){
	// 把父类赋值给自己
	nospam = nospam.parentNode;
}
// 如果 nospam 不为 null
if(nospam != null){
	// 调用 下面的方法
	_addonclick();
}

function _onclick(){
	if(window.tID){
		// 如果 tID 不为空，清除之
		clearTimeout(window.tID);
	}
	// __onclick 未定义或 null 返回 false
	if(typeof(window.__onclick) == 'undefined' || window.__onclick == null){
		return false;
	}
	// 循环调用 __onclick 方法
	for(var i=0;i < window.__onclick.length;i++){
		window.__onclick[i]();
	}
}

function _addonclick(){
	// 获得  nospam 中所有 input 为对象
	var fi = nospam.getElementsByTagName('input');
	// 循环处理 input 数组
	for(var i=0; i<fi.length; i++ ){
		// 如果 input 的 type 等于 submit 或 image
		if(fi[i].type=='submit' || fi[i].type=='image'){
			// 赋值 setnospam 方法到 __onclick 数组中
			window.__onclick[0] = setnospam;
			// 如果 fi 定义了 onclick 事件且不为 null
			if(typeof(fi.onclick) != 'undefined' && fi.onclick != null)
				// 把事件赋值给 __onclick 数组。
				window.__onclick[1] = fi.onclick;
			// 赋值 _onclick 方法到 onfocus （获得焦点）事件
			fi[i].onfocus = _onclick;
			// onblur （失去焦点）事件
			fi[i].onblur = function(){
				window.tID = window.setTimeout(function(){
					document.getElementById(nospam_id).value = 0;
				}, 3000);
			}
			break;
		}
	}
	return true;
}

function setnospam(){
	// 获得  nospam 中所有 input 对象
	var fi = nospam.getElementsByTagName('input');
	// 循环处理 input 数组
	for(var i=0; i<fi.length; i++ ){
		// 如果 input 的 name 不为空且等于 post_id
		if(fi[i].name!='' && fi[i].name==check_field){
			// 把 该值赋给 #comment_nospam 并终止循环
			document.getElementById(nospam_id).value = fi[i].value;
			break;
		}
	}
	// 返回 true
	return true;
}