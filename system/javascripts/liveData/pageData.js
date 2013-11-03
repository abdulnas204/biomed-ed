// SpryPagedView.js - version 0.7 - Spry Pre-Release 1.6.1
//
// Copyright (c) 2006. Adobe Systems Incorporated.
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//
//   * Redistributions of source code must retain the above copyright notice,
//     this list of conditions and the following disclaimer.
//   * Redistributions in binary form must reproduce the above copyright notice,
//     this list of conditions and the following disclaimer in the documentation
//     and/or other materials provided with the distribution.
//   * Neither the name of Adobe Systems Incorporated nor the names of its
//     contributors may be used to endorse or promote products derived from this
//     software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
// LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
// CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
// SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
// INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
// CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
// POSSIBILITY OF SUCH DAMAGE.

var Spry;if(!Spry)Spry={};if(!Spry.Data)Spry.Data={};Spry.Data.PagedView=function(a,b){Spry.Data.DataSet.call(this);this.ds=a;this.pageSize=10;this.pageOffset=0;this.forceFullPages=false;this.pageFirstItemOffset=0;this.useZeroBasedIndexes=false;this.setCurrentRowOnPageChange=false;Spry.Utils.setOptions(this,b);this.adjustmentValue=1;if(!this.useZeroBasedIndexes)this.adjustmentValue=0;this.pageStop=this.pageOffset+this.pageSize;this.ds.addObserver(this);this.preProcessData();if(this.pageSize>0)this.filter(this.getFilterFunc())};Spry.Data.PagedView.prototype=new Spry.Data.DataSet();Spry.Data.PagedView.prototype.constructor=Spry.Data.PagedView;Spry.Data.PagedView.prototype.setCurrentRow=function(a){if(this.ds)this.ds.setCurrentRow(a)};Spry.Data.PagedView.prototype.setCurrentRowNumber=function(a){if(this.ds)this.ds.setCurrentRowNumber(a)};Spry.Data.PagedView.prototype.sort=function(a,b){if(!a)return;if(typeof a=="string")a=[a,"ds_RowID"];else if(a.length<2&&a[0]!="ds_RowID")a.push("ds_RowID");if(!b)b="toggle";if(b=="toggle"){if(this.lastSortColumns.length>0&&this.lastSortColumns[0]==a[0]&&this.lastSortOrder=="ascending")b="descending";else b="ascending"}var c={oldSortColumns:this.lastSortColumns,oldSortOrder:this.lastSortOrder,newSortColumns:a,newSortOrder:b};this.notifyObservers("onPreSort",c);this.disableNotifications();Spry.Data.DataSet.prototype.sort.call(this,a,b);this.updatePagerColumns();this.firstPage();this.enableNotifications();this.notifyObservers("onPostSort",c)};Spry.Data.PagedView.prototype.loadData=function(){if(!this.ds||this.ds.getLoadDataRequestIsPending())return;if(!this.ds.getDataWasLoaded()){this.ds.loadData();return}Spry.Data.DataSet.prototype.loadData.call(this)};Spry.Data.PagedView.prototype.onDataChanged=function(a,b){this.setPageOffset(0);this.preProcessData()};Spry.Data.PagedView.prototype.onCurrentRowChanged=function(a,b){var c=this;setTimeout(function(){c.notifyObservers("onCurrentRowChanged",b)},0)};Spry.Data.PagedView.prototype.onPostSort=Spry.Data.PagedView.prototype.onDataChanged;Spry.Data.PagedView.prototype.updatePagerColumns=function(){var a=this.getData(true);if(!a||a.length<1)return;var b=a.length;var c=(this.pageSize>0)?this.pageSize:b;var d=1;var e=d+c-1;e=(e<d)?d:(e>b?b:e);var f=1;var g=parseInt((b+c-1)/c);var h=Math.min(b,c);for(var i=0;i<b;i++){itemIndex=i+1;if(itemIndex>e){d=itemIndex;e=d+this.pageSize-1;e=(e>b)?b:e;h=Math.min(e-d+1,c);++f}var j=a[i];if(j){j.ds_PageNumber=f;j.ds_PageSize=this.pageSize;j.ds_PageItemRowNumber=i;j.ds_PageItemNumber=itemIndex;j.ds_PageFirstItemNumber=d;j.ds_PageLastItemNumber=e;j.ds_PageItemCount=h;j.ds_PageCount=g;j.ds_PageTotalItemCount=b}}};Spry.Data.PagedView.prototype.preProcessData=function(){if(!this.ds||!this.ds.getDataWasLoaded())return;this.notifyObservers("onPreLoad");this.unfilteredData=null;this.data=[];this.dataHash={};var a=this.ds.getData();if(a){var b=a.length;for(var i=0;i<b;i++){var c=a[i];var d=new Object();Spry.Utils.setOptions(d,c);this.data.push(d);this.dataHash[d.ds_RowID]=d}if(b>0)this.curRowID=a[0].ds_RowID;this.updatePagerColumns()}this.loadData()};Spry.Data.PagedView.prototype.getFilterFunc=function(){var d=this;return function(a,b,c){if(c<d.pageOffset||c>=d.pageStop)return null;return b}};Spry.Data.PagedView.prototype.setPageOffset=function(a){var b=this.getData(true).length;this.pageFirstItemOffset=(a<0)?0:a;if(this.forceFullPages&&a>(b-this.pageSize))a=b-this.pageSize;if(a<0)a=0;this.pageOffset=a;this.pageStop=a+this.pageSize};Spry.Data.PagedView.prototype.filterDataSet=function(a){if(this.pageSize<1)return;this.setPageOffset(a);var b=this.getData(true);if(b&&b.length&&b[this.pageFirstItemOffset])this.curRowID=b[this.pageFirstItemOffset].ds_RowID;if(this.setCurrentRowOnPageChange)this.ds.setCurrentRow(this.curRowID);this.filter(this.getFilterFunc())};Spry.Data.PagedView.prototype.getPageCount=function(){return parseInt((this.getData(true).length+this.pageSize-1)/this.pageSize)};Spry.Data.PagedView.prototype.getCurrentPage=function(){return parseInt((((this.pageFirstItemOffset!=this.pageOffset)?this.pageFirstItemOffset:this.pageOffset)+this.pageSize)/this.pageSize)-this.adjustmentValue};Spry.Data.PagedView.prototype.goToPage=function(a){a=parseInt(a);var b=this.getPageCount();if((a+this.adjustmentValue)<1||(a+this.adjustmentValue)>b)return;var c=(a-1+this.adjustmentValue)*this.pageSize;this.filterDataSet(c)};Spry.Data.PagedView.prototype.goToPageContainingRowID=function(a){this.goToPageContainingRowNumber(this.getRowNumber(this.getRowByID(a),true))};Spry.Data.PagedView.prototype.goToPageContainingRowNumber=function(a){this.goToPage(this.getPageForRowNumber(a))};Spry.Data.PagedView.prototype.goToPageContainingItemNumber=function(a){this.goToPageContainingRowNumber(a-1)};Spry.Data.PagedView.prototype.firstPage=function(){this.goToPage(1-this.adjustmentValue)};Spry.Data.PagedView.prototype.lastPage=function(){this.goToPage(this.getPageCount()-this.adjustmentValue)};Spry.Data.PagedView.prototype.previousPage=function(){this.goToPage(this.getCurrentPage()-1)};Spry.Data.PagedView.prototype.nextPage=function(){this.goToPage(this.getCurrentPage()+1)};Spry.Data.PagedView.prototype.getPageForRowID=function(a){return this.getPageForRowNumber(this.getRowNumber(this.getRowByID(a),true))};Spry.Data.PagedView.prototype.getPageForRowNumber=function(a){return parseInt(a/this.pageSize)+1-this.adjustmentValue};Spry.Data.PagedView.prototype.getPageForItemNumber=function(a){return this.getPageForRowNumber(a-1)};Spry.Data.PagedView.prototype.getPageSize=function(){return this.pageSize};Spry.Data.PagedView.prototype.setPageSize=function(a){if(this.pageSize==a)return;if(a<1){this.pageSize=0;this.setPageOffset(0);this.updatePagerColumns();this.filter(null)}else if(this.pageSize<1){this.pageSize=a;this.setPageOffset(0);this.updatePagerColumns();this.filterDataSet(this.pageOffset)}else{this.pageSize=a;this.updatePagerColumns();this.goToPage(this.getPageForRowNumber(this.pageFirstItemOffset))}};Spry.Data.PagedView.prototype.getPagingInfo=function(){return new Spry.Data.PagedView.PagingInfo(this)};Spry.Data.PagedView.PagingInfo=function(a){Spry.Data.DataSet.call(this);this.pagedView=a;a.addObserver(this)};Spry.Data.PagedView.PagingInfo.prototype=new Spry.Data.DataSet();Spry.Data.PagedView.PagingInfo.prototype.constructor=Spry.Data.PagedView.PagingInfo;Spry.Data.PagedView.PagingInfo.prototype.onDataChanged=function(a,b){this.extractInfo()};Spry.Data.PagedView.PagingInfo.prototype.onPostSort=Spry.Data.PagedView.PagingInfo.prototype.onDataChanged;Spry.Data.PagedView.PagingInfo.prototype.extractInfo=function(){var d=this.pagedView;if(!d||!d.getDataWasLoaded())return;this.notifyObservers("onPreLoad");this.unfilteredData=null;this.data=[];this.dataHash={};var e=d.getData(true);if(e){var f=e.length;var g=d.getPageCount();var i=0;var h=0;while(i<f){var j=e[i];var k=new Object();k.ds_RowID=h++;this.data.push(k);this.dataHash[k.ds_RowID]=k;k.ds_PageNumber=j.ds_PageNumber;k.ds_PageSize=j.ds_PageSize;k.ds_PageCount=j.ds_PageCount;k.ds_PageFirstItemNumber=j.ds_PageFirstItemNumber;k.ds_PageLastItemNumber=j.ds_PageLastItemNumber;k.ds_PageItemCount=j.ds_PageItemCount;k.ds_PageTotalItemCount=j.ds_PageTotalItemCount;i+=k.ds_PageSize}if(f>0){var l=this;var m=function(a,b,c){if(a!="onPostLoad")return;l.removeObserver(m);l.setCurrentRowNumber(d.getCurrentPage()-(d.useZeroBasedIndexes?0:1))};this.addObserver(m)}}this.loadData()};

function filterData() {
	var input = document.getElementById("keywords");
	if (!input.value) {
		dsUsers.filter(null);
		return;
	}

	var replacementString = input.value;
	
	if (!document.getElementById("contains").checked) {
		replacementString = "^" + replacementString;
	}

	var replacement = new RegExp(replacementString, "i");
	var dataSet = document.getElementById("searchBy").value;
	
	var filter = function(dataInput, row, rowNumber) {
		var data = row[dataSet];
		
		if (data && data.search(replacement) != -1) {
			return row;
		}
		
		return null;
	};

	dsUsers.filter(filter);
}

function startFilterTimer() {
	if (startFilterTimer.timerID) {
		clearTimeout(startFilterTimer.timerID);
	}
	
	startFilterTimer.timerID = setTimeout(
		function() {
			startFilterTimer.timerID = null;
			filterData();
		}
	, 100);
}