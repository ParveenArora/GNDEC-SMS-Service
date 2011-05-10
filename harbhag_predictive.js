/* This is the file which is being used to produce Google Like
 * Predective search results
 * Author: Harbhag Singh Sohal
 * License : GPL V3 
 */
 
var phone_nos = new Array();
var phone_no;

function checkbox(objid) { 
	var x=new Array();
	x=document.getElementById(objid);
		if (x.checked == true)
		{
		if(x.value != '')
		phone_nos.push(x.value);
		}
		if (x.checked == false)
		{
		if(x.value != '') {
		index = phone_nos.indexOf(x.value);
		phone_nos.splice(index,1); }
		}
}
	
function sendsms(objid,type) {
	var x = document.getElementById(objid);
	phone_no = phone_nos.join(',');
	x.value = phone_no;
	document.getElementById("schedule_send").value=type;
}
	
function checka() {
	phone_nos = [];
	var node_list = document.getElementsByTagName('input');
    for (var i = 0; i < node_list.length; i++) 
    {
    var node = node_list[i];
	if (node.getAttribute('type') == 'checkbox') 
	{
	node.checked = true;
	if(node.getAttribute('value') != '')
	phone_nos.push(node.getAttribute('value'));
	}
	}
}
	
function unchecka() {
	phone_nos = [];
	var node_list = document.getElementsByTagName('input');
    for (var i = 0; i < node_list.length; i++) 
    {
    var node = node_list[i];
	if (node.getAttribute('type') == 'checkbox') 
	node.checked = false;
	}
}






function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your Browser Does not Support AJAX");
	}
}


var searchReq = getXmlHttpRequestObject();

function handleSearchSuggest() {
	if (searchReq.readyState == 4) {
		var ss = document.getElementById('suggestions')
		ss.innerHTML = '';
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) {
			var suggest = '<div id="f" onmouseover="javascript:suggestOver(this);" ';
			suggest += 'onmouseout="javascript:suggestOut(this);" ';
			suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			suggest += 'class="suggest_link" style="cursor:default">' + str[i] + '</div>';
			ss.innerHTML += suggest;
		}
	}
}


function suggestOver(div_value) {
	div_value.className = 'suggest_link_over';
}

function suggestOut(div_value) {
	div_value.className = 'suggest_link';
}
 
function setSearch(value) {
	var ts = document.getElementById('search').value;
	var tss = ts.replace(/[a-z]/g,'');
	var test = value.split(',');
	var f_val = test[1];
	document.getElementById('search').value = tss+f_val+',';
	document.getElementById('suggestions').innerHTML = '';
}

function searchSuggest() {
	var str = document.getElementById("search");
	var temp = str.value.split(",");
	var numb = temp.length;
	var final = temp[numb-1];
	if (searchReq.readyState == 4 || searchReq.readyState == 0) {
		var str = document.getElementById('search').value;
		searchReq.open("GET", 'lookup.php?q=' + final, true);
		searchReq.onreadystatechange = handleSearchSuggest; 
		searchReq.send(null);
	}		
}

function validate_mobile(type) {
	var mob_val = document.getElementById('search').value;
	var wrong_mob = [];
	var num_array = "1234567890"
	var first_digit = "987";
	if(mob_val=='') {
		alert("Please add atleast one Receiver");
		return false;
	}
	else {
		var mob_array = mob_val.split(',');
		for(i=0;i<=mob_array.length-1;i++) {
			var mob_arr = mob_array[i].split('');
			var count = 0;
			for(j=0;j<=mob_arr.length-1;j++) {
				if(j==0) {
					if(mob_arr[j]=='+') {
						wrong_mob.push("\n"+mob_array[i]+" (No Need to add '+' or '91')");
						count++;
					}
					if(count==0 && first_digit.indexOf(mob_arr[j])=='-1') {
						wrong_mob.push("\n"+mob_array[i]+" (Mobile No. Should Start With 9 or 8 or 7 only)");
						count++;
					}
				}
				if(j!=0 && count==0){
					if(num_array.indexOf(mob_arr[j])=='-1') {
						wrong_mob.push("\n"+mob_array[i]+" (Contains Invalid Characters)");
						count++;
					}
				}
				if(j==mob_arr.length-1 && count==0) {
					if(mob_arr.length!=10 && count==0) {
						wrong_mob.push("\n"+mob_array[i]+" (No. of digits not equal to 10)");
						count++;
					}
				}
			}
		}
		if(wrong_mob.length!=0) {
			alert("These are the wrong Mobile Numbers, Please correct them befor sending SMS:\n"+wrong_mob);
			return false;
		}
	}
	if(type=='schedule') {
		var tim_e = document.getElementById('dt').value;
		if(tim_e=='') {
			alert("Please Enter Time");
			return false;
		}
	}
}
