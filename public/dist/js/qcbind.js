webpackJsonp([10],{

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(7);
	__webpack_require__(3);
	$(function(){

		// 获取验证码
		var snTrue = true;
		$("#btnSend").on(click, function(){
			var $this = $(this);
			var $name = $("#txtUserName");
			var username = $name.val();
			var testEmail = _email.test(username);
			var ajaxNum;

			// 判断是否是手机
			if( username.length==11 && _sizeTel.test(username) ){
				ajaxNum = 1;
		    }else if( testEmail ){
		    	ajaxNum = 0;
		    }else{
		    	$name.focus();
		    	lifocus(0,'请输入正确的手机号或邮箱地址');
		        return false;
		    }
		    if( !ajaxlack ||!snTrue ) return; ajaxlack = false; snTrue = false;
		    $name.parents("li").find(".orange").hide();

		    ajaxCodeFun(username,ajaxNum,function(){
				$this.addClass("current");
	        	setIntervalFun(120,function(_TIME){
	        		$this.html('重新发送('+_TIME+')');
	        		if( _TIME<=0 ){
	        			$this.removeClass("current");
	        			$this.html('重新发送');
	        			snTrue = true;
	        		}
	        	});
			});
		});

		$('#username, #pwd').keydown(function(e){
			if( e.keyCode==13 ){
				loginFun();
			}
		});

		// 登录函数
		function loginFun(){
			var username = $("#txtUserName").val();
			var password = $("#txtPwd").val();
			var testEmail = _email.test(username);
			var dataObj;		
			// 判断是否是手机
			if( username.length==11 && _sizeTel.test(username) ){
				dataObj = {
					mobile : username,
					password : password
				}
		    }else if( testEmail ){
		    	dataObj = {
					password : password,
					email : username
				}
		    }else{
		    	$("#txtUserName").focus();
		    	lifocus(0,'请输入正确的手机号或邮箱地址');
		        return;
		    }	

		    if( password=='' ){
		    	$("#txtPwd").focus();
		    	lifocus(1,'密码不正确！');
		    	return;
			}	

			if( !ajaxlack ) return; ajaxlack = false;
			$("#btnBind").html('正在绑定···').css({"letter-spacing":"0"});
			$.ajax({
		        url: "/member/service/qcbindaccount",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(160,60,'绑定成功！','/');
		        }else{
		        	$("#txtPwd").focus();
		    		lifocus(2,'密码不正确！');
		        }
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    	$("#btnBind").html('绑定').attr("style","");
		    });
		}

		// 验证
		function verificationFun(_focus){
			var txtUserName = $("#txtUserName").val();
			var txtPwd = $("#txtPwd").val();
			var txtConPwd = $("#txtConPwd").val();
			var txtCode = $("#txtCode").val();
			var vcode = $("#txtvcode").val();
			var testEmail = _email.test(txtUserName);
			var dataObj;

			if( txtUserName.length==11 && _sizeTel.test(txtUserName) ){
				dataObj = {
					mobile : txtUserName,
					password : txtPwd,
					password_confirm : txtConPwd,
					captcha : txtCode,
					vcode : vcode
				}
		    }else if( testEmail ){
		    	dataObj = {
					password : txtPwd,
					email : txtUserName,
					password_confirm : txtConPwd,
					captcha : txtCode,
					vcode : vcode
				}
		    }else{
		    	$("#txtUserName").focus();
		    	lifocus(0,'请输入正确的手机号或邮箱地址');
		    	return;
		    }

		    if( txtUserName=='' ){
		    	$("#txtUserName").focus();
		    	lifocus(0,'请输入您的手机号/邮箱地址');
		    	return;
		    }

		    if( vcode=="" ){
		    	$("#txtvcode").focus();
		    	lifocus(1,'请认真核对验证码是否正确');
		    	return;
		    }

		    if( txtPwd=='' ){
		    	$("#txtPwd").focus();
		    	lifocus(2,'请设置登录密码');
		    	return;
		    }

		    if( txtConPwd==''||txtConPwd.length<8||txtConPwd.length>20 ){
		    	$("#txtPwd").focus();
		    	lifocus(2,'密码由8-20位字母');
		    	return;
		    }

		    if( txtPwd!=txtConPwd ){
		    	$("#txtConPwd").focus();
		    	lifocus(3,'两次输入的密码不一致');
		    	return;
		    }

		    if( txtCode=='' ){
		    	$("#txtCode").focus();
		    	lifocus(4,'请输入验证码');
		    	return;
		    }

		    agreeFun(dataObj);
		}

		function lifocus(eq,text){
			var $li = $(".register-form-con li");
			$li.eq(eq).addClass("enter-focus error-text").find(".orange").html(errorIcon+text).show();
		}

		// 注册函数
		function agreeFun(dataObj){
			if( !ajaxlack ) return; ajaxlack = false;
			$(".register-form-con li").find('.orange').hide();
			$.ajax({
		        url: "/member/service/qcbindaccount",
		        type: 'POST',
		        dataType: 'json',
		        data: dataObj
		    })
		    .done(function(data) {
		        console.log(data);
		        if( data.success ){
		        	FailDialog(160,60,'绑定成功！','/');
		        }else{
		        	console.log(data.error_msg);
		        	if( data.error_code==513 ){
		        		$("#txtvcode").focus();
		        		lifocus(1,'请认真核对验证码是否正确');
		        	}else if( data.error_code==597 ){
						lifocus(4,'请认真核对验证码是否正确');
		        	}else{
		        		alert('提交失败，请重试！');
		        	}
		        }
		    })
		    .fail(function() {
		        alert('网络错误！')
		    })
		    .always(function() {
		    	ajaxlack = true;
		    });
		}


		// 刷新验证码
		$("#imgCode, #btnChangeCode").on(click, function(){
			$("#imgCode").attr({"src":$("#imgCode").attr("src")});
		});


		// 账号验证
		var verifiedby = true;
		$("#txtUserName").on('blur', function(){
			var $this = $(this);
			var thisParent = $this.parent();
			var txtUserName = $this.val();
			var testEmail = _email.test(txtUserName);

			if( txtUserName.length==11 && _sizeTel.test(txtUserName) ){
				ajaxNum = 0;
		    }else if( testEmail ){
		    	ajaxNum = 1;
		    }else{
		    	if( $this.val()=='' ) return;
		    	lifocus(0,'请输入正确的手机号或邮箱地址');
		    	return;
		    }

		    checkEmailMobileFun(txtUserName,ajaxNum,function(data){
		    	if( data.success ){
		    		verifiedby = false;
		    		$(".liHide").show();
		        }else{
		        	verifiedby = true;
		        	$(".liHide").hide();
		        }
		    });
		    
		});

		// 已有账户还是注册账户
		$("#btnBind").on('click', function(){
			if( verifiedby ){ //已有账户
				loginFun();
			}else{ //没有账户
				verificationFun();
			}
		});
		



		// 失去焦点
		$("#txtPwd").on("blur", function(){
			var txtPwd = $("#txtPwd").val();
			var txtConPwd = $("#txtConPwd").val();
			if( txtPwd=='' ) return;
			$("#pwdStrength").hide();
			if( txtPwd.length<8||txtPwd.length>20 ){
				lifocus(2,'密码由8-20位字母');
				$(".register-form-con li:eq(1)").removeClass('correct-text');
				return;
			}
			if( txtPwd.length>8&&txtPwd.length<20 ){
				$(".register-form-con li:eq(1)").addClass('correct-text').find(".passport-icon").show();
				return;
			}
		});

		// 密码强度验证
		$("#txtPwd").on("keyup", function(){
			var txtPwd = $("#txtPwd").val();
			var $div = $("#pwdStrength");
			if( txtPwd.length>8&&txtPwd.length<=20 ){
				var qiang = passwordsFun(txtPwd);
				console.log(passwordsFun(txtPwd));
				$div.show();
				if( qiang=="弱" ){
					$div.html(yellowTxt(0));
				}else if( qiang=="中" ){
					$div.html(yellowTxt(1));
				}else{
					$div.html(yellowTxt(2));
				}
			}else{
				$div.html(yellowTxt(0));
			}
		});

		$("#txtConPwd").on("blur", function(){
			var txtPwd = $("#txtPwd").val();
			var txtConPwd = $("#txtConPwd").val();
			if( txtConPwd=='' ) return;
			if( txtPwd==txtConPwd ){
		    	$(".register-form-con li:eq(2), .register-form-con li:eq(3)").addClass('correct-text').find(".passport-icon").show();
		    }else{
		    	$("#txtConPwd").parent().addClass("enter-focus error-text").find(".orange").html(errorIcon+'两次输入的密码不一致').show();
		    	$("#txtConPwd").parent().removeClass('correct-text');
		    }
		});
		








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

/***/ }

});