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
			var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
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
