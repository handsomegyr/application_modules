webpackJsonp([11],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {var $id = $("#pServerTime span");
	function userTime(uTime){
		var myDate = new Date(uTime*1000);
		var hours = myDate.getHours();
		var minutes = myDate.getMinutes();
		var second = myDate.getSeconds();
		$id.eq(0).html(hours);
		$id.eq(1).html(minutes);
		$id.eq(2).html(second);
	}
	var d = 1455774938;
	setInterval(function(){
		d++;
		userTime(d);
	},1000);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }
]);