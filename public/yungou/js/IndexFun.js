var mainHttp = "http://www.1yyg.com";
(function() {
	if (window.self != window.top) {
		var b = mainHttp;
		if (typeof(window.location) == "object") {
			b = window.location.href
		}
		var a = $("<form name='toTopUrl' method='get' action='" + b + "' target='_top'></form>");
		a.appendTo("body").ready(function() {
			a.submit()
		})
	}
})();

function GetJPData(d, c, a, b) {
	$.getJSON(d + "/JPData?action=" + c + (a != "" ? "&" : "") + a + "&fun=?", b)
}
var loadImgFun = function() {
		var e = "src2";
		var b = $("#loadingPicBlock");
		if (b.length > 0) {
			var c = b.find("img");
			var d = function() {
					return Math.max(document.documentElement.scrollTop, document.body.scrollTop)
				};
			var a = function() {
					return document.documentElement.clientHeight + d() + 100
				};
			var f = d();
			var g = f;
			var h = function() {
					c.each(function() {
						if ($(this).parent().offset().top <= a()) {
							var i = $(this).attr(e);
							if (i) {
								$(this).attr("src", i).removeAttr(e).show()
							}
						}
					})
				};
			$(window).bind("scroll", function() {
				g = d();
				if (g - f > 50) {
					f = g;
					h()
				}
			});
			h()
		}
	};
var _IsCartChanged = true;
var _IsBuySubmiting = false;
var _InsertIntoCart = function() {};
var _GetUserLoginInfo = function() {};
var _IeVersion = $.browser.msie && parseInt($.browser.version);
(function() {
	var A = "http://cart.1yyg.com";
	var B = "https://passport.1yyg.com";
	var C = "http://member.1yyg.com";
	var aa = "http://skin.1yyg.com";
	var b = "http://api.1yyg.com";
	var R = function() {
			if ($.browser.msie && $(window).width() < 1190) {
				if (parseInt($.browser.version) < 9) {
					$("body").addClass("f-width-change")
				} else {
					$("body").removeClass("f-width-change")
				}
			} else {
				$("body").removeClass("f-width-change")
			}
		};
	if ($.browser.msie && parseInt($.browser.version) == 6) {
		$("body").css("font", '12px/150% Arial,Verdana,"\5b8b\4f53"')
	}
	R();
	$(window).resize(R);
	loadImgFun();
	var M = $("#rightTool");
	var o = $("#divRTool");
	var ai = $("body").attr("rf");
	var v = !(ai == "1" || ai == "2");
	var Z = function() {
			var aD = false;
			var aE = function() {
					if (aD || $(window).scrollTop() > 0) {
						M.children("div").height($(window).height());
						M.fadeIn("slow")
					} else {
						if (!aD) {
							M.fadeOut("slow")
						}
					}
				};
			var aF = function() {
					if ($(window).scrollTop() > 0) {
						o.fadeIn("slow")
					} else {
						if (!aD) {
							o.fadeOut("slow")
						}
					}
				};
			var aC = function() {
					if (ai == "2") {
						aD = true;
						aE()
					} else {
						if (ai == "1") {
							aE()
						} else {
							aF()
						}
					}
				};
			$(window).scroll(aC).resize(function() {
				_IsCartChanged = true;
				aC()
			});
			aC()
		};
	var T = location.href.toLowerCase();
	var D = T.indexOf("member.1yyg.com") > -1 && T.indexOf("referauth.do") == -1 && T.indexOf("referrals.do") == -1 ? true : false;
	var I = T.indexOf("u.1yyg.com") > -1 ? true : false;
	var aj = $("#ulTopRight");
	var al = $("#liTopUMsg");
	if (D || I) {
		al.remove();
		al.length = 0;
		$("#liHome").show().next().show();
		$("#lizxkf").prev().show().next().next().show();
		if (D) {
			o.remove()
		}
	} else {
		$("#liMember").show().next().show();
		$("#lizxkf").show().prev().show()
	}
	if (!D) {
		$("#liCCTV").show().prev().show()
	}
	_GetUserLoginInfo = function() {
		if (aj.length > 0) {
			GetJPData(b, "logininfo", "", function(aC) {
				aj.children('li[type="nologin"]').remove();
				if (aC.code == 1) {
					aj.prepend('<li><div class="u-menu-hd u-menu-login"><a href="http://member.1yyg.com/" title="' + aC.username + '" class="blue"><span class="fl"><img src="http://faceimg.1yyg.com/userface/30/' + aC.userPhoto + '"><s class="transparent-png"></s></span>' + aC.username + '</a><a href="' + B + '/Logout.html" title="退出">[退出]</a></div></li><li class="f-gap"><s></s></li>');
					al.attr("show", "1").show().next("li.f-gap").show()
				} else {
					if (aC.code == 0) {
						aj.prepend('<li type="nologin"><div class="u-menu-hd"><a href="' + B + '/login.html?forward=rego" title="登录">登录</a></div></li><li type="nologin" class="f-gap"><s></s></li><li type="nologin"><div class="u-menu-hd"><a href="' + B + '/register.html?forward=rego" title="注册">注册</a></div></li><li class="f-gap"><s></s></li>');
						al.attr("show", "-1").hide()
					}
				}
			})
		}
	};
	_GetUserLoginInfo();
	var S = $("#ulHTotalBuy");
	var O = $("#spFundTotal");
	var aw = 0;
	var a = 2000;
	var ab = false;
	var Y = function() {
			GetJPData(b, "totalBuyCount", "", function(aI) {
				if (aI.state == 0) {
					O.html("￥" + aI.fundTotal);
					var aE = aI.count;
					if (aw != aE) {
						if (!ab) {
							var aM = aE.toString().length;
							if (aM > 9) {
								var aL = "";
								for (var aH = 0; aH < aM - 9; aH++) {
									aL += '<li class="num" type="add"><cite style="top: -243px;">';
									for (var aJ = 9; aJ >= 0; aJ--) {
										aL += '<em t="' + aJ + '">' + aJ + "</em>"
									}
									aL += "</cite><i></i></li>";
									aw = "0" + aw.toString()
								}
								aL += '<li class="nobor">,</li>';
								S.children("li").eq(0).after(aL);
								ab = true
							}
						}
						if (aw == 0) {
							aw = aE;
							S.children("li.num").each(function() {
								var aN = '<cite style="top:-243px;">';
								for (var aO = 9; aO >= 0; aO--) {
									aN += '<em t="' + aO + '">' + aO + "</em>"
								}
								aN += "</cite><i></i>";
								$(this).html(aN)
							});
							var aG = aE.toString();
							var aF = aG.length;
							var aD = aG.split("");
							S.find("cite").each(function(aQ, aN) {
								var aP = $(this);
								var aO = parseInt(aD[aQ]);
								if (!(/^\d+$/.test(aO))) {
									aO = 0
								}
								aP.animate({
									top: "-" + (27 * (9 - aO)) + "px"
								}, {
									queue: false,
									duration: a,
									complete: function() {}
								})
							})
						} else {
							var aC = aw.toString().split("");
							var aK = aE.toString().split("");
							aw = aE;
							S.find("cite").each(function(aS, aP) {
								var aT = 0;
								var aR = parseInt(aC[aS]);
								if (aC[aS] <= aK[aS]) {
									aT = parseInt(aK[aS]) - parseInt(aC[aS])
								} else {
									aT = 10 + parseInt(aK[aS]) - parseInt(aC[aS])
								}
								if (aT != 0) {
									var aU = $(this).children('em[t="' + aR + '"]');
									var aO = aU.nextAll();
									for (var aQ = aO.length - 1; aQ > -1; aQ--) {
										$(this).prepend($(aO[aQ]))
									}
									var aN = -(243 - aT * 27);
									$(this).css({
										top: "-243px"
									}).animate({
										top: aN
									}, {
										queue: false,
										duration: a,
										complete: function() {}
									})
								}
							})
						}
					}
				}
			});
			setTimeout(Y, 5000)
		};
	if (S.length > 0 || O.length > 0) {
		Y()
	}
	var ap = function(aI) {
			var aH = "汽车";
			var aD = "输入“汽车”试试";
			var aC = 50;
			var aG = function() {
					aI.unbind("blur").bind("focus", aF);
					if (aI.val() == "") {
						aI.val(aD).css({
							color: "#BBB",
							padding: "9px 130px 9px 5px",
							width: "125px"
						});
						aI.next("span").css("zIndex", "10").show()
					}
				};
			var aF = function() {
					aI.unbind("focus").bind("blur", aG).css({
						color: "#333",
						padding: "9px 0px 9px 5px",
						width: "255px"
					});
					if (aI.val() == aD) {
						aI.val("").next("span").css("zIndex", "0").hide()
					}
				};
			var aE = function() {
					var aJ = $.trim(aI.val());
					if (aJ == aD) {
						aJ = aH
					}
					location.href = mainHttp + "/search/?q=" + encodeURIComponent(aJ.replace(/(\/)/g, ""))
				};
			aI.focus(aF).keydown(function(aJ) {
				if (13 == ((window.event) ? event.keyCode : aJ.keyCode)) {
					aE()
				}
			}).keyup(function() {
				var aJ = $(this).val().trim();
				if (aJ.length > aC) {
					$(this).val(aJ.substring(0, aC))
				}
			}).attr("placeholder", "").css("color", "#BBB").val(aD);
			$("#btnHSearch").click(function() {
				aE();
				return false
			})
		};
	var H = $("#txtHSearch");
	if (H.length > 0) {
		ap(H)
	}
	var t = $("#btnTFavorite");
	if (t.length > 0) {
		var ak = function() {
				var aE = "1元云购";
				var aC = "http://www.1yyg.com";
				try {
					window.external.addFavorite(aC, aE)
				} catch (aD) {
					try {
						window.sidebar.addPanel(aE, aC, "")
					} catch (aD) {
						alert("抱歉，您所使用的浏览器无法完成此操作。\r\n\r\n加入收藏失败，请使用Ctrl + D进行添加！")
					}
				}
			};
		t.bind("click", function() {
			ak()
		})
	}
	var ah = function(aC) {
			if (aC && aC.stopPropagation) {
				aC.stopPropagation()
			} else {
				window.event.cancelBubble = true
			}
		};
	var ad = function() {
			$("#liMobile").hover(function() {
				$(this).addClass("u-arr-hover")
			}, function() {
				$(this).removeClass("u-arr-hover")
			});
			$("#liMember").hover(function() {
				$(this).addClass("u-arr-hover")
			}, function() {
				$(this).removeClass("u-arr-hover")
			});
			al.hover(function() {
				$(this).addClass("u-arr-hover")
			}, function() {
				$(this).removeClass("u-arr-hover")
			})
		};
	var X = $("#divSortList");
	if ($("body.home").length == 0) {
		$("#divGoodsSort").hover(function() {
			X.show()
		}, function() {
			setTimeout(function() {
				if (!k) {
					X.hide()
				}
			}, 200)
		})
	}
	var k = false;
	X.children("dl").each(function() {
		$(this).hover(function(aC) {
			k = true;
			ah(aC);
			$(this).addClass("hover");
			if ($(this).next().length > 0) {
				$(this).append("<i></i>")
			}
		}, function(aC) {
			k = false;
			$(this).removeClass("hover").children("i").remove()
		})
	});
	var aq = $("#pServerTime");
	if (aq.length > 0) {
		var m = aq.children("span");
		var u = function(aF) {
				var aD = aF.getHours();
				var aC = aF.getMinutes();
				var aE = aF.getSeconds();
				return (aD > 9 ? aD.toString() : "0" + aD) + ":" + (aC > 9 ? aC.toString() : "0" + aC) + ":" + (aE > 9 ? aE.toString() : "0" + aE)
			};
		var P = 0;
		var z = new Date();
		var V = z.getFullYear() + "-" + (z.getMonth() + 1) + "-" + z.getDate() + " " + u(z);
		GetJPData(b, "servertime", "time=" + V, function(aD) {
			if (aD.code == 0) {
				P = aD.num
			}
			var aC = function() {
					var aG = new Date();
					aG.setSeconds(aG.getSeconds() + P);
					var aF = aG.getHours();
					var aE = aG.getMinutes();
					var aH = aG.getSeconds();
					m.eq(0).html(aF > 9 ? aF.toString() : "0" + aF);
					m.eq(1).html(aE > 9 ? aE.toString() : "0" + aE);
					m.eq(2).html(aH > 9 ? aH.toString() : "0" + aH)
				};
			setInterval(aC, 1000)
		})
	}
	var p = $("#spHCart");
	var x = function() {
			GetJPData(A, "cartnum", "", function(aC) {
				var aD = 0;
				if (aC.code == 0) {
					aD = aC.num
				} else {
					if (aC.code == -1) {
						aD = 0
					}
				}
				if (aD > 0) {
					var aE = aD > 99 ? "..." : aD;
					p.html("(" + aE + ")");
					f.html(aE);
					if (aD > 0) {
						f.show()
					}
				} else {
					p.html("");
					f.html("0").hide()
				}
			})
		};
	var i = $("#ulRToolList");
	var w = i.find(".f-shopping-cart");
	var f = w.find("em");
	var L = $("#divRTCartMain");
	var aA = $("#divRTLogin");
	if (w.length > 0) {
		var am = false;
		var q = false;
		var E = "";
		var ar = 37;
		var c = L.find(".cartEmpty");
		if (_IeVersion === 6) {
			ar = 36
		}
		var W = function() {
				if (aA.attr("isOpen") != "1") {
					q = false;
					E = setTimeout(function() {
						if (!q) {
							Q(0)
						}
					}, 800)
				}
			};
		var y = function() {
				am = true;
				L.miniCartTool();
				L.hover(function() {
					clearTimeout(E);
					q = true;
					L.stop().animate({
						right: ar
					}, 400)
				}, W)
			};
		w.bind("mouseenter", function() {
			clearTimeout(E);
			q = true;
			Q(1, true);
			if (!am) {
				Base.getScript("http://skin.1yyg.com/js/cartTool.js?date=150703", y)
			} else {
				L.miniCartTool()
			}
		}).bind("mouseleave", W);
		_InsertIntoCart = function() {
			x()
		}
	}
	var Q = function(aD, aC) {
			if (aD == 0) {
				aA.hide();
				L.css("zIndex", "5").stop().animate({
					right: "-240px"
				}, {
					duration: aC ? 300 : 400,
					queue: false,
					complete: function() {
						w.removeClass("f-checked");
						L.hide();
						ax.css("zIndex", "10")
					}
				})
			} else {
				ax.css("zIndex", "5").stop().animate({
					right: "-240px"
				}, {
					duration: aC ? 300 : 400,
					queue: false,
					complete: function() {
						l.removeClass("cart-hover").find("b").hide();
						ax.hide().attr("isShowed", "0")
					}
				});
				L.css("zIndex", "10")
			}
		};
	var l = i.find(".f-attention");
	var ax = $("#divRTColect");
	if (l.length > 0) {
		var G = true;
		var au = false;
		var r = false;
		var af = "";
		var ae = 37;
		if (_IeVersion === 6) {
			ae = 36
		}
		var ac = function() {
				r = false;
				if (G) {
					af = setTimeout(function() {
						if (!r) {
							if ($("#fastLoginFrame").length == 0) {
								Q(1)
							}
						}
					}, 800)
				} else {
					l.removeClass("cart-hover").find("b").hide();
					ax.hide().attr("isShowed", "0")
				}
			};
		var an = false;
		var K = function() {
				au = true;
				ax.miniCollectTool(an);
				ax.hover(function() {
					clearTimeout(af);
					r = true;
					l.addClass("cart-hover").find("b").show();
					ax.stop().animate({
						right: ae
					}, 400)
				}, ac)
			};
		l.bind("mouseenter", function() {
			clearTimeout(af);
			r = true;
			Q(0, true);
			var aC = function() {
					if (!au) {
						Base.getScript("http://skin.1yyg.com/js/CollectTool.js?date=150703", K)
					} else {
						ax.miniCollectTool(an)
					}
				};
			GetJPData("http://api.1yyg.com", "hasCollectGoods", "", function(aD) {
				if (aD.code == 0) {
					an = true;
					aC()
				} else {
					if (aD.code == 10) {
						aC()
					} else {
						G = false
					}
				}
			})
		}).bind("mouseleave", ac)
	}
	var az = function() {
			var aC = [A];
			var aE = true;
			for (var aD = 0; aD < aC.length; aD++) {
				if (location.href.toLowerCase().indexOf(aC[aD]) > -1) {
					aE = false;
					break
				}
			}
			if (aE) {
				x()
			}
		};
	if (w.length > 0 || p.length > 0) {
		az()
	}
	var J = function() {
			if (location.href.toLowerCase().indexOf("passport.1yyg.com") == -1) {
				var aE = $.cookie("_topNotice1");
				var aC = "-1px";
				if (aE == null) {
					var aD = $('<div class="banner-prompt clrfix"><div class="w1190"><span class="fl">2015年春节放假及发货安排公告：根据国家法定节假日规定并结合公司具体情况，1元云购2015年春节放假及发货时间安排……<a href="http://group.1yyg.com/topic-8874.html" target="_blank" class="ckxq">查看详情</a></span><span class="fr"><a href="javascript:;" class="close" title="关闭"></a></span></div></div>');
					aD.find("a.close").click(function() {
						aD.remove();
						$.cookie("_topNotice1", "1", {
							domain: "1yyg.com",
							expires: 10,
							path: "/"
						});
						$("body").attr("style", "background:url(http://skin.1yyg.com/images/bg_2015.gif?v=150211) center -1px no-repeat !important");
						return false
					});
					$("div.g-toolbar").before(aD);
					aC = "35px"
				}
				if ($("body").hasClass("home")) {
					$("body").attr("style", "background:url(http://skin.1yyg.com/images/bg_2015.gif?v=150211) center " + aC + " no-repeat !important")
				}
			}
		};
	var ao = function() {
			if (al.length > 0) {
				var aD = al.attr("show");
				if (aD != "-1") {
					if (aD == "1") {
						var aC = function() {
								var aG = $.cookie("_msgFApply");
								var aM = $.cookie("_msgSys");
								var aF = $.cookie("_msgFPriv");
								var aH = $.cookie("_msgReply");
								var aJ = 0;
								if (aM && parseInt(aM) > 0) {
									aJ += parseInt(aM)
								}
								if (aG && parseInt(aG) > 0) {
									aJ += parseInt(aG)
								}
								if (aH && parseInt(aH) > 0) {
									aJ += parseInt(aH)
								}
								if (aF && parseInt(aF) > 0) {
									aJ += parseInt(aF)
								}
								var aL = "";
								var aK = function(aO, aP, aN) {
										return '<span><a href="' + C + aO + '" title="' + aP + '">' + aP + (aN && parseInt(aN) > 0 ? "<em" + (parseInt(aN) > 99 ? ' class="three-digit">...' : ">" + aN) + "</em>" : "") + "</a></span>"
									};
								var aI = "";
								aI += aK("/UserMessage.do", "系统消息", aM);
								aI += aK("/FriendsApply.do", "好友请求", aG);
								aI += aK("/ReplyCommentsMsg.do", "评论回复", aH);
								aI += aK("/UserPrivMsg.do", "私信", aF);
								al.children("div.u-select").html(aI);
								if (aJ > 0) {
									if (al.children("div.u-menu-hd").find("em").length == 0) {
										al.children("div.u-menu-hd").append("<em></em>")
									}
								} else {
									al.children("div.u-menu-hd").find("em").remove()
								}
							};
						var aE = function() {
								GetJPData(b, "ckmsg", "", function(aF) {
									if (aF.code == 0) {
										$.cookie("_msgTip", null, {
											domain: "1yyg.com"
										});
										aC()
									}
									setTimeout(aE, 30000)
								})
							};
						aE()
					} else {
						setTimeout(ao, 1000)
					}
				}
			}
		};
	var at = function() {
			if (M.length > 0 || o.length > 0) {
				Z()
			}
			ad();
			ao();
			var aC = function(aF) {
					var aE = new Date();
					aF.attr("src", aF.attr("data") + "?v=" + GetVerNum()).removeAttr("id").removeAttr("data")
				};
			var aD = $("#pageJS", "head");
			if (aD.length > 0) {
				aC(aD)
			} else {
				aD = $("#pageJS", "body");
				if (aD.length > 0) {
					aC(aD)
				}
			}
		};
	//Base.getScript(aa + "/JS/Comm.js?date=20151019", at);
	if (v) {
		o.find("li").each(function() {
			$(this).hover(function() {
				$(this).addClass("cart-hover")
			}, function() {
				$(this).removeClass("cart-hover")
			})
		})
	} else {
		if (M.length > 0) {
			GetJPData("http://poster.1yyg.com", "getbysortid", "ID=4", function(aD) {
				if (aD.state == 0) {
					var aC = aD.listItems[0];
					if (aC.type == 0) {
						M.find("div.u-sentence").html("<cite><a " + (aC.url == "" ? "" : 'href="' + aC.url + '" target="_blank"') + ' title="' + aC.alt + '"><img width="' + aC.width + '" height="' + aC.height + '" src="' + aC.src + '" /></a></cite>')
					}
				}
			})
		}
		i.children("li:gt(0)").each(function(aC) {
			$(this).hover(function() {
				$(this).addClass("cart-hover")
			}, function() {
				if (aC == 0 && $("#colect_shower").children("ul").length > 0) {
					return
				}
				$(this).removeClass("cart-hover")
			})
		})
	}
	var av = "btnTopQQ";
	var h = "btnRigQQ";
	var aB = "btnRigQQ2";
	var g = "btnBtmQQ";
	var F = $("#" + av);
	var e = $("#" + h);
	var s = $("#" + aB);
	var U = $("#" + g);
	if (F.length > 0 || e.length > 0 || U.length > 0) {
		var j = function() {
				var aC = "4006859800";
				BizQQWPA.addCustom([{
					aty: "0",
					nameAccount: aC,
					selector: av
				}, {
					aty: "0",
					nameAccount: aC,
					selector: h
				}, {
					aty: "0",
					nameAccount: aC,
					selector: aB
				}, {
					aty: "0",
					nameAccount: aC,
					selector: g
				}])
			};
		var d = new Date();
		var N = d.getHours();
		var n = d.getMinutes();
		if ((N == 8 && n > 30) || (N > 8 && N < 21)) {
			F.removeClass("u-service-off").addClass("u-service-on");
			U.removeClass("u-service-off").addClass("u-service-on");
			e.attr("class", "u-customer-on");
			s.attr("class", "u-customer-on")
		} else {
			F.html("<i></i>离线留言").attr("title", "离线留言");
			U.html("<i></i>离线留言").attr("title", "离线留言")
		}
		var ay = false;
		var ag = function() {
				if (ay) {
					return false
				}
				ay = true;
				Base.getScript("http://wpa.b.qq.com/cgi/wpa.php", j)
			};
		F.hover(ag, null);
		e.hover(function() {
			$(this).addClass("cart-hover");
			ag()
		}, function() {
			$(this).removeClass("cart-hover")
		});
		U.hover(ag, null);
		if (v) {
			s.hover(function() {
				$(this).addClass("cart-hover");
				ag()
			}, function() {
				$(this).removeClass("cart-hover")
			})
		}
	}
	if (v) {
		o.find("li.f-back-to").click(function() {
			$("body,html").animate({
				scrollTop: 0
			}, 0);
			return false
		})
	} else {
		i.children("li.f-back-to").click(function() {
			$("body,html").animate({
				scrollTop: 0
			}, 0);
			return false
		})
	}
	$('input[type="password"]').bind("cut copy paste", function(aC) {
		aC.preventDefault()
	})
})();