/* This is the file which is being used to produce Google Like
 * Predective search results
 * Author: Harbhag Singh Sohal
 * License : GPL V3 
 */

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
			suggest += 'class="suggest_link">' + str[i] + '</div>';
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
	var num_array = "1234567890+"
	if(mob_val=='') {
		alert("Please add atleast one Receiver");
		return false;
	}
	else {
		var mob_array = mob_val.split(',');
		for(i=0;i<=mob_array.length-1;i++) {
			var mob_arr = mob_array[i].split('');
			for(j=0;j<=mob_arr.length-1;j++) {
				var count = 0;
				if(num_array.indexOf(mob_arr[j])=='-1') {
					count++;
				}
			}
			if(count!=0) {
				wrong_mob.push(mob_array[i]+"(Contains Invalid Character) ");
			}
			else {
				if(mob_arr.length<10 && mob_arr.length!=0 ) {
					wrong_mob.push(mob_array[i]+"(Contains <10 Character) ");
				}
				if(mob_arr.length>13) {
					wrong_mob.push(mob_array[i]+"(Contains >13 Character) ");
				}
			}
		}
		if(wrong_mob.length!=0) {
			alert("These are the wrong Mobile Numbers, Please correct them befor sending SMS: "+wrong_mob);
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
