/*
LICENSE: See "license.php" located at the root installation

This is a jQuery plugin to create a sortable, paginated table from JSON encoded input
*/

var table;
var template;
var indexes;
var limit;
var extend;
var currentPage;

(function($) {
	$.fn.pagination = function(JSON, options) {
		extend = $.extend($.fn.pagination.defaults, options);
		
		return this.each(function() {
		//Hide the table, wrap it and other necessary componenets inside of handler <div>s
			table = $(this);
			table.hide();
			table.wrap('<div style="padding: 30px 0px 80px 0px; text-align:center;" id="container"><div id="tableBuilder"></div></div>');
			
			var container = $('div#container div#tableBuilder:has(table#' + table.attr('id') + ')');
			container.prepend('<div id="loadingText">' + extend.loadingHTML + '</div><div id="navigationTop"></div>');
			container.append('<div id="navigationBottom"></div>');
			
		//Grab the JSON	array
			$.getJSON(JSON, function(data) {
			//Grab the provided template row to later replace its values
				template = $('div#container div#tableBuilder table#' + table.attr('id') + ' tr:has(td)').html();
				
			//Add the necessary classes to the sortable headers
				$('div#container div#tableBuilder table#' + table.attr('id') + ' tr th span').addClass(extend.sortableClass);
				$('div#container div#tableBuilder table#' + table.attr('id') + ' tr th span#' + extend.sortBy).removeClass().addClass(extend.sortAscending);
				
			//Grab all of the indexes from the JSON array to detirmine which which values will be replaced in the template row
				indexes = [];
				
				$.each(data[0], function(index, value) {
					indexes.push(index);
				});
				
			//Sort the array by the default criteria
				data = dataSort(data, extend.sortBy);
				
			//Fill the table, using JavaScript for improved performance
				limit = data.length - 1 > extend.displayMax - 1 ? extend.displayMax - 1 : data.length - 1;
				
				buildTable(data, 1);
				
			//Add the pagination
				currentPage = 1;
				var totalPages = (data.length - 1)/extend.displayMax > 1 ? Math.ceil((data.length - 1)/extend.displayMax) : 1;
				
				pagination(totalPages);
				
			//Style the table, hide the loader, then display the table
				$('div#container div#loadingText').hide();
				$('div#container').css('padding', '0px');
				table.show();
				
			//Listen for commands to sort the data by a new criteria
				$('div#container div#tableBuilder table#' + table.attr('id') + ' tr th span').click(function() {
					var sortHeader = $(this);
					
					$('table#' + table.attr('id') + ' tr th span:not(#' + sortHeader.attr('id') + ')').removeClass().addClass(extend.sortableClass);
					
					if(!sortHeader.hasClass(extend.sortAscending)) {
						sortHeader.removeClass().addClass(extend.sortAscending);
						data = dataSort(data, sortHeader.attr('id'));
					} else {
						sortHeader.removeClass().addClass(extend.sortDescending);
						data = dataSort(data, sortHeader.attr('id'));
						data.reverse();
					}
					
					buildTable(data, currentPage);
				});
				
			//Listen for commands to jump to different pages
				$('div#container div#navigationTop span:not(#next):not(#previous)').click(function() {
					var navigation = $(this);
					var otherNaviation = $('div#container div#navigationBottom span:not(#next):not(#previous)');
					currentPage = navigation.attr('id');
					
					navigation.parent().children(':not(#next):not(#previous)').removeClass().addClass(extend.paginatePages);
					navigation.addClass(extend.paginateActivePage);
					otherNaviation.parent().children(':not(#next):not(#previous)').removeClass().addClass(extend.paginatePages);
					otherNaviation.filter('#' + currentPage).addClass(extend.paginateActivePage);
					
					pagination(totalPages, navigation.attr('id'));
					buildTable(data, currentPage);
				});
				
			//Open the search dialog
				$(extend.searchTrigger).click(function() {
					$(extend.searchDialog).dialog({
						modal : false,
						resizable : false,
						width : extend.dialogWidth,
						height : extend.dialogHeight,
						buttons : {
							'Close' : function() {
								$(extend.searchDialog).dialog('close');
							}
						}
					});
				});
			});
		});
	};
	
//Sort the array by the given criteria
	function dataSort(inputArray, criteria) {
		inputArray.sort(function(a, b) {
			if(isNaN(a[criteria])) {
				if (a[criteria] < b[criteria]) {
					return -1;
				} else if (a[criteria] > b[criteria]) {
					return 1;
				} else {
					return 0;
				}
			} else {
				if (parseInt(a[criteria]) < parseInt(b[criteria])) {
					return -1;
				} else if (parseInt(a[criteria]) > parseInt(b[criteria])) {
					return 1;
				} else {
					return 0;
				}
			}
		});
		
		return inputArray;
	}
	
//Build the table
	function buildTable(data, page) {
		$('table#' + table.attr('id') + ' tr:has(td)').remove();
		
		for(var count = (page * limit) - limit; count <= (page * limit); count ++) {
			if(count < data.length) {
				var modifyTemplate = decodeURI(template);
				
				for(var i = 0; i < indexes.length - 1; i ++) {
					modifyTemplate = modifyTemplate.replace(new RegExp('{' + indexes[i] + '}', 'gi'), data[count][indexes[i]]);
				}
				
				$('<tr></tr>').html(modifyTemplate).appendTo('table#' + table.attr('id'));
			} else {
				break;
			}
		}
		
		$('table#' + table.attr('id') + ' tr:gt(1):even').addClass(extend.even);
		$('table#' + table.attr('id') + ' tr:gt(1):odd').addClass(extend.odd);
	}
	
//Build the pagination navigation
	function pagination(totalPages, start) {
		$('div#container div#navigationTop span:not(#next):not(#previous)').remove();
		$('div#container div#navigationBottom span:not(#next):not(#previous)').remove();
		
		var pagination = '';
		var counter = 0;
		var upperLimit = Math.ceil((extend.paginateLimit) / 2) + parseInt(start);
		var lowerLimit = parseInt(start) - Math.ceil((extend.paginateLimit) / 2);
		
		if(isNaN(lowerLimit)) {
			upperLimit = totalPages;
			lowerLimit = 1;
		}
		
		if(lowerLimit <= 0) {
			upperLimit += Math.abs(lowerLimit);
			lowerLimit = 1;
		}
		alert(upperLimit);
		if((upperLimit >= totalPages - Math.ceil((extend.paginateLimit) / 2)) && totalPages > extend.paginateLimit) {
			lowerLimit -= totalPages - (totalPages - Math.ceil((extend.paginateLimit) / 2));
		}
		
		if(upperLimit = totalPages) {
			upperLimit = totalPages;
		}
		
		if(lowerLimit >= 3) {
			pagination += '<span id="1" class="' + extend.paginatePages + '">1</span> ... ';
		} else if(lowerLimit >= 2) {
			pagination += '<span id="1" class="' + extend.paginatePages + '">1</span>';
		}
		
		for(var count = lowerLimit; count <= upperLimit; count ++) {
			if(counter <= extend.paginateLimit) {
				if(count == currentPage) {
					pagination += '<span id="' + count + '" class="' + extend.paginateActivePage + '">' + count + '</span>';
				} else {
					pagination += '<span id="' + count + '" class="' + extend.paginatePages + '">' + count + '</span>';
				}
			} else {
				if(totalPages == extend.paginateLimit + 1) {
					pagination += '<span id="' + count + '" class="' + extend.paginatePages + '">' + count + '</span>';
				}
				
				break;
			}
			
			counter ++;
		}
		
		if(upperLimit < totalPages) {
			pagination += ' ... <span id="' + totalPages + '" class="' + extend.paginatePages + '">' + totalPages + '</span>';
		}
		
		$('#navigationTop').html(pagination);
		$('#navigationBottom').html(pagination);
	}
	
//Plugin default options
	$.fn.pagination.defaults = {
		loadingHTML : 'Loading data...',
		sortBy : 'id',
		displayMax : 20,
		
		sortableClass : 'sortHover',
		sortAscending : 'ascending',
		sortDescending : 'descending',
		even : 'even',
		odd : 'odd',
		
		paginateLimit : 10,
		paginatePages : 'searchNumber',
		paginateActivePage : 'currentSearchNumber',
		
		searchTrigger : '.search',
		searchDialog : '#searchDialog',
		
		dialogWidth : 600,
		dialogHeight : 225
	};
})(jQuery);