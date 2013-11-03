function toggleNumericalDiv(field) {
	if (field && document.getElementById('contentHide')) {
		switch (field) {
			case "unlimited" : document.getElementById('contentHide').className = "contentShow"; break;
			case "one" : document.getElementById('contentHide').className = "contentHide"; break;
			case "two" : document.getElementById('contentHide').className = "contentShow"; break;
			case "three" : document.getElementById('contentHide').className = "contentShow"; break;
			case "four" : document.getElementById('contentHide').className = "contentShow"; break;
			case "five" : document.getElementById('contentHide').className = "contentShow"; break;
			case "six" : document.getElementById('contentHide').className = "contentShow"; break;
			case "seven" : document.getElementById('contentHide').className = "contentShow"; break;
			case "eight" : document.getElementById('contentHide').className = "contentShow"; break;
			case "nine" : document.getElementById('contentHide').className = "contentShow"; break;
			case "ten" : document.getElementById('contentHide').className = "contentShow"; break;
		}
	}
}

function toggleSimpleDiv(field) {
	if (field && document.getElementById('contentHide')) {
		switch (field) {
			case "1" : document.getElementById('contentHide').className = "contentShow"; break;
			case "0" : document.getElementById('contentHide').className = "contentHide"; break;
		}
	}
}

function toggleTypeDiv(field) {
	if (field && document.getElementById('contentAdvanced') && document.getElementById('contentMessage')) {
		switch (field) {
			case "Custom Content" : document.getElementById('contentAdvanced').className = "contentShow"; 
									document.getElementById('contentMessage').className = "contentHide";
									break;
			case "Login" : document.getElementById('contentAdvanced').className = "contentHide"; 
						   document.getElementById('contentMessage').className = "noResults contentShow";
						   break;
			case "Register" : document.getElementById('contentAdvanced').className = "contentShow"; 
							  document.getElementById('contentMessage').className = "contentHide";
							  break;
		}
	}
}