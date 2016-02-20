webpackJsonp([7],{

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(22);
	__webpack_require__(3);
	$(function(){
		var pageGet = $.query.get("page");
		var forward = $.query.get("forward");
		var $btnSubmitMsg = $("#btnSubmitMsg");
		var $saytext = $("#saytext");
		// 好友私信
		if( pageGet=='friend'||pageGet=='userprivmsgdetail' ){
			var msgToUID = $.query.get("msgToUID");
			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = $("#saytext").val();
		        insertuserprivatemsgAjaxFun(msgToUID,replace_em(str),function(data){
		        	if( data.success ){
						if( pageGet=='friend' ){
							parent.window.friendMessageFun();
						}else if( pageGet=='userprivmsgdetail' ){
							parent.window.userprivmsgdetailMessageFun();
						}
					}else{
						
					}
		        });
		    });

		    // 私信好友
			function insertuserprivatemsgAjaxFun(msgToUID,msgContent,Callback){
				if( !ajaxlack ) return; ajaxlack = false;
				$.ajax({
			        url: "/message/service/insertuserprivatemsg",
			        type: 'POST',
			        dataType: 'json',
			        data: { msgToUID:msgToUID, msgContent:encodeURIComponent(msgContent) }
			    })
			    .done(function(data) {
			        console.log(data);
			        Callback(data);
			    })
			    .fail(function() {
			        alert('网络错误！')
			    })
			    .always(function() {
			    	ajaxlack = true;
			    });
			}
		}

		// 晒单回复
		if( pageGet=="postdetaila" ){

			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = replace_em($("#saytext").val());
		        parent.window.wininsertpostreplyaFun(encodeURIComponent(str));
		    });  
		}

		// 晒单回复回复
		if( pageGet=="postdetailb" ){

			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = replace_em($("#saytext").val());
		        parent.window.wininsertpostreplybFun(encodeURIComponent(str));
		    });  
		}


		// 个人主页发私信
		if( pageGet=="uMy" ){
			var msgTouserWeb = $.query.get("msgTouserWeb");
			$btnSubmitMsg.on('click',function(){
				if( $(this).hasClass("disBtn") ) return;
		        var str = $("#saytext").val();
		        insertmsgAjaxFun(msgTouserWeb,replace_em(str),function(data){
		        	if( data.success ){
						parent.window.insertmsgFun();
					}else{
						FailDialog(180,60,'发送失败！');
					}
		        });
		    });   

		    // 私信好友
			function insertmsgAjaxFun(msgTouserWeb,msgContent,Callback){
				if( !ajaxlack ) return; ajaxlack = false;
				$.ajax({
			        url: "/message/service/insertuserprivatemsg",
			        type: 'POST',
			        dataType: 'json',
			        data: { msgToUID:msgTouserWeb, msgContent:encodeURIComponent(msgContent) }
			    })
			    .done(function(data) {
			        console.log(data);
			        Callback(data);
			    })
			    .fail(function() {
			        alert('网络错误！')
			    })
			    .always(function() {
			    	ajaxlack = true;
			    });
			}

		}

		$saytext.on("blur input keydown", function() {
			if( $(this).val()!=''||$(this).val().length>2 ){
				$btnSubmitMsg.removeClass('disBtn');
			}else{
				$btnSubmitMsg.addClass('disBtn');
			}	
		});



		$('.emotion').qqFace({
		    id : 'facebox', 
		    assign:'saytext', 
		    path:'/qqFace/arclist/' //表情存放的路径
		});
		//查看结果
		function replace_em(str){
		    str = str.replace(/\</g,'&lt;');
		    str = str.replace(/\>/g,'&gt;');
		    str = str.replace(/\n/g,'<br/>');
		    str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/qqFace/arclist/$1.gif" border="0" />');
		    return str;
		}






































	});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },

/***/ 7:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {/**
	* http://plugins.jquery.com/project/query-object
	* jQuery.query - Query String Modification and Creation for jQuery
	* Written by Blair Mitchelmore (blair DOT mitchelmore AT gmail DOT com)
	* Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
	* Date: 2009/8/13
	*
	* @author Blair Mitchelmore
	* @version 2.1.7
	** jquery.query-2.1.7.js**/
	new function(settings) {
	    // Various Settings
	    var $separator = settings.separator || '&';
	    var $spaces = settings.spaces === false ? false : true;
	    var $suffix = settings.suffix === false ? '' : '[]';
	    var $prefix = settings.prefix === false ? false : true;
	    var $hash = $prefix ? settings.hash === true ? "#" : "?" : "";
	    var $numbers = settings.numbers === false ? false : true;

	    jQuery.query = new function() {
	        var is = function(o, t) {
	            return o != undefined && o !== null && (!!t ? o.constructor == t : true);
	        };
	        var parse = function(path) {
	            var m, rx = /\[([^[]*)\]/g , match = /^([^[]+)(\[.*\])?$/ .exec(path), base = match[1], tokens = [];
	            while (m = rx.exec(match[2])) tokens.push(m[1]);
	            return [base, tokens];
	        };
	        var set = function(target, tokens, value) {
	            var o, token = tokens.shift();
	            if (typeof target != 'object') target = null;
	            if (token === "") {
	                if (!target) target = [];
	                if (is(target, Array)) {
	                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
	                } else if (is(target, Object)) {
	                    var i = 0;
	                    while (target[i++] != null) ;
	                    target[--i] = tokens.length == 0 ? value : set(target[i], tokens.slice(0), value);
	                } else {
	                    target = [];
	                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
	                }
	            } else if (token && token.match( /^\s*[0-9]+\s*$/ )) {
	                var index = parseInt(token, 10);
	                if (!target) target = [];
	                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
	            } else if (token) {
	                var index = token.replace( /^\s*|\s*$/g , "");
	                if (!target) target = { };
	                if (is(target, Array)) {
	                    var temp = { };
	                    for (var i = 0; i < target.length; ++i) {
	                        temp[i] = target[i];
	                    }
	                    target = temp;
	                }
	                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
	            } else {
	                return value;
	            }
	            return target;
	        };

	        var queryObject = function(a) {
	            var self = this;
	            self.keys = { };

	            if (a.queryObject) {
	                jQuery.each(a.get(), function(key, val) {
	                    self.SET(key, val);
	                });
	            } else {
	                jQuery.each(arguments, function() {
	                    var q = "" + this;
	                    q = q.replace( /^[?#]/ , ''); // remove any leading ? || #
	                    q = q.replace( /[;&]$/ , ''); // remove any trailing & || ;
	                    if ($spaces) q = q.replace( /[+]/g , ' '); // replace +'s with spaces

	                    jQuery.each(q.split( /[&;]/ ), function() {
	                        var key = decodeURIComponent(this.split('=')[0] || "");
	                        var val = decodeURIComponent(this.split('=')[1] || "");

	                        if (!key) return;

	                        if ($numbers) {
	                            if ( /^[+-]?[0-9]+\.[0-9]*$/ .test(val)) // simple float regex
	                                val = parseFloat(val);
	                            else if ( /^[+-]?[0-9]+$/ .test(val)) // simple int regex
	                                val = parseInt(val, 10);
	                        }

	                        val = (!val && val !== 0) ? true : val;

	                        if (val !== false && val !== true && typeof val != 'number')
	                            val = val;

	                        self.SET(key, val);
	                    });
	                });
	            }
	            return self;
	        };

	        queryObject.prototype = {
	            queryObject: true,
	            has: function(key, type) {
	                var value = this.get(key);
	                return is(value, type);
	            },
	            GET: function(key) {
	                if (!is(key)) return this.keys;
	                var parsed = parse(key), base = parsed[0], tokens = parsed[1];
	                var target = this.keys[base];
	                while (target != null && tokens.length != 0) {
	                    target = target[tokens.shift()];
	                }
	                return typeof target == 'number' ? target : target || "";
	            },
	            get: function(key) {
	                var target = this.GET(key);
	                if (is(target, Object))
	                    return jQuery.extend(true, { }, target);
	                else if (is(target, Array))
	                    return target.slice(0);
	                return target;
	            },
	            SET: function(key, val) {
	                var value = !is(val) ? null : val;
	                var parsed = parse(key), base = parsed[0], tokens = parsed[1];
	                var target = this.keys[base];
	                this.keys[base] = set(target, tokens.slice(0), value);
	                return this;
	            },
	            set: function(key, val) {
	                return this.copy().SET(key, val);
	            },
	            REMOVE: function(key) {
	                return this.SET(key, null).COMPACT();
	            },
	            remove: function(key) {
	                return this.copy().REMOVE(key);
	            },
	            EMPTY: function() {
	                var self = this;
	                jQuery.each(self.keys, function(key, value) {
	                    delete self.keys[key];
	                });
	                return self;
	            },
	            load: function(url) {
	                var hash = url.replace( /^.*?[#](.+?)(?:\?.+)?$/ , "$1");
	                var search = url.replace( /^.*?[?](.+?)(?:#.+)?$/ , "$1");
	                return new queryObject(url.length == search.length ? '' : search, url.length == hash.length ? '' : hash);
	            },
	            empty: function() {
	                return this.copy().EMPTY();
	            },
	            copy: function() {
	                return new queryObject(this);
	            },
	            COMPACT: function() {

	                function build(orig) {
	                    var obj = typeof orig == "object" ? is(orig, Array) ? [] : { } : orig;
	                    if (typeof orig == 'object') {

	                        function add(o, key, value) {
	                            if (is(o, Array))
	                                o.push(value);
	                            else
	                                o[key] = value;
	                        }

	                        jQuery.each(orig, function(key, value) {
	                            if (!is(value)) return true;
	                            add(obj, key, build(value));
	                        });
	                    }
	                    return obj;
	                }

	                this.keys = build(this.keys);
	                return this;
	            },
	            compact: function() {
	                return this.copy().COMPACT();
	            },
	            toString: function() {
	                var i = 0, queryString = [], chunks = [], self = this;
	                var encode = function(str) {
	                    str = str + "";
	                    if ($spaces) str = str.replace( / /g , "+");
	                    return encodeURIComponent(str);
	                };
	                var addFields = function(arr, key, value) {
	                    if (!is(value) || value === false) return;
	                    var o = [encode(key)];
	                    if (value !== true) {
	                        o.push("=");
	                        o.push(encode(value));
	                    }
	                    arr.push(o.join(""));
	                };
	                var build = function(obj, base) {
	                    var newKey = function(key) {
	                        return !base || base == "" ? [key].join("") : [base, "[", key, "]"].join("");
	                    };
	                    jQuery.each(obj, function(key, value) {
	                        if (typeof value == 'object')
	                            build(value, newKey(key));
	                        else
	                            addFields(chunks, newKey(key), value);
	                    });
	                };

	                build(this.keys);

	                if (chunks.length > 0) queryString.push($hash);
	                queryString.push(chunks.join($separator));

	                return queryString.join("");
	            }
	        };

	        return new queryObject(location.search, location.hash);
	    };
	}(jQuery.query || { });  // Pass in jQuery.query as settings object
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },

/***/ 22:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(jQuery) {// QQ表情插件
	(function($){
		$.fn.qqFace = function(options){
			var defaults = {
				id : 'facebox',
				path : 'face/',
				assign : 'content',
				tip : 'em_'
			};
			var option = $.extend(defaults, options);
			var assign = $('#'+option.assign);
			var id = option.id;
			var path = option.path;
			var tip = option.tip;
			
			if(assign.length<=0){
				alert('缺少表情赋值对象。');
				return false;
			}
			
			$(this).click(function(e){
				var strFace, labFace;
				if($('#'+id).length<=0){
					strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
								  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
					for(var i=1; i<=45; i++){
						labFace = '['+tip+i+']';
						strFace += '<td><img src="'+path+i+'.gif" onclick="$(\'#'+option.assign+'\').setCaret();$(\'#'+option.assign+'\').insertAtCaret(\'' + labFace + '\');" /></td>';
						if( i % 15 == 0 ) strFace += '</tr><tr>';
					}
					strFace += '</tr></table></div>';
				}
				$(this).parent().append(strFace);
				var offset = $(this).position();
				var top = offset.top + $(this).outerHeight();
				$('#'+id).css('bottom',"30px");
				$('#'+id).css('left',offset.left);
				$('#'+id).show();
				e.stopPropagation();
			});

			$(document).click(function(){
				$('#'+id).hide();
				$('#'+id).remove();
			});
		};

	})(jQuery);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }

});