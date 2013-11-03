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

function toggleAlphaDiv(field) {
	if (field && document.getElementById('contentHide')) {
		switch (field) {
			case "yes" : document.getElementById('contentHide').className = "contentShow"; break;
			case "no" : document.getElementById('contentHide').className = "contentHide"; break;
		}
	}
}