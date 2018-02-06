var ws = {};
var client_user_id = 0;
var userlist = {};
var GET = getRequest();
var face_count = 19;
var params;
var user_id = 1;
var user = null;

$(document).ready(function () {
    //使用原生WebSocket
    if (window.WebSocket || window.MozWebSocket)
    {
        ws = new WebSocket(webim.server);
    }
    //使用flash websocket
    else if (webim.flash_websocket)
    {
        WEB_SOCKET_SWF_LOCATION = "/live/static/flash-websocket/WebSocketMain.swf";
        $.getScript("/live/static/flash-websocket/swfobject.js", function () {
            $.getScript("/live/static/flash-websocket/web_socket.js", function () {
                ws = new WebSocket(webim.server);
            });
        });
    }
    //使用http xhr长轮循
    else
    {
        ws = new Comet(webim.server);
    }
	
	params = getRequest()
	user_id = params['user_id']
	
	if(user_id == 1){
		user = user1;
	}else{
		user = user2;
	}
	
    listenEvent();
});

function listenEvent() {
    /**
     * 连接建立时触发
     */
    ws.onopen = function (e) {
        //连接成功
        console.log("connect webim server success.");
        //发送登录信息
		online();
    };

    //有消息到来时触发
    ws.onmessage = function (e) {
        var message = $.evalJSON(e.data);
        var cmd = message.cmd;
        if (cmd == 'login')
        {
            //client_user_id = $.evalJSON(e.data).fd;
            ////获取在线列表
            //ws.send($.toJSON({cmd : 'getOnlineList'}));
            ////获取历史记录
            //ws.send($.toJSON({cmd : 'getHistoryList'}));
            //alert( "收到消息了:"+e.data );
        }        
        else if (cmd == 'newUser')
        {
            showNewUser(message);
        }
        else if (cmd == 'fromMsg')
        {						
            showNewMsg(message);
        }        
        else if (cmd == 'error')
        {
			console.log("onerror: " + message.code + message.msg);
			alert("onerror: " + message.code + message.msg);
        }
        else if (cmd == 'getVersion')
        {
			console.log("version: " + message.version);
			alert("version: " + message.version);
        }
        else if (cmd == 'setVip')
        {
			console.log("setVip: " + message.user_id);
			alert("setVip: " + message.user_id);
        }        
		else if (cmd == 'online')
        {
			if(message.userInfo.user_id == user.user_id){
				client_user_id = message.userInfo.user_id;
				console.log("client_user_id: " + client_user_id);
			}
			message.data = "上线了";
			
			showNewUser(message);			
            showNewMsg(message);
        }
		else if (cmd == 'come')
        {			
			message.data = "来了";			
            showNewMsg(message);
        }
		else if (cmd == 'offline')
        {
            var cid = message.userInfo.user_id;
            delUser(cid);
			if(message.msg_type == 'system_user_offline'){
				message.data = "下线了";
			}
            showNewMsg(message);
        }
		else if (cmd == 'getOnlineList')
        {
            showOnlineList(message);
        }
		else if (cmd == 'chat')
        {						
            showNewMsg(message);
        }
        else if (cmd == 'getChatHistoryList')
        {
            showChatHistory(message);
        }
		else if (cmd == 'barrage')
        {						
            showNewMsg(message);
        }
        else if (cmd == 'getBarrageHistoryList')
        {
            showBarrageHistory(message);
        }
		else if (cmd == 'notice')
        {						
            showNewMsg(message);
			
        }
		else if (cmd == 'welcome')
        {
			console.log("welcome ");
			alert("welcome ");
        }
		else if (cmd == 'like')
        {
			message.data = "给主播点了一个赞";
			showNewMsg(message);
        }  
		else if (cmd == 'closeLive')
        {
			console.log("closeLive ");			
			//player.close();
			var myVideo = document.getElementById("videoPlayer");
			myVideo.pause();
			alert("closeLive ");
        }
		
		else if (cmd == 'pauseLive')
        {
			console.log("pauseLive ");			
			var myVideo = document.getElementById("videoPlayer");
			myVideo.pause();			
			alert("pause_live ");
        }		
		else if (cmd == 'resumeLive')
        {
			console.log("resumeLive ");			
			var myVideo = document.getElementById("videoPlayer");
			myVideo.play();			
			alert("resume_live ");
        }
    };

    /**
     * 连接关闭事件
     */
    ws.onclose = function (e) {
        $(document.body).html("<h1 style='text-align: center'>连接已断开，请刷新页面重新登录。</h1>");
    };

    /**
     * 异常事件
     */
    ws.onerror = function (e) {
        $(document.body).html("<h1 style='text-align: center'>服务器[" + webim.server +
            "]: 拒绝了连接. 请检查服务器是否启动. </h1>");
        console.log("onerror: " + e.data);
    };
}

document.onkeydown = function (e) {
    var ev = document.all ? window.event : e;
    if (ev.keyCode == 13) {
        sendMsg($('#msg_content').val(), 'chat','text');
        return false;
    } else {
        return true;
    }
};

function selectUser(userid) {
    $('#userlist').val(userid);
}

/**
 * 显示所有在线列表
 * @param dataObj
 */
function showOnlineList(dataObj) {
	$(".nav-header").html("Chats:"+dataObj.number+"人数");
	
    var li = '';
    var option = "<option value='0' id='user_all' >所有人</option>";

    for (var i = 0; i < dataObj.list.length; i++) {
		//userlist[dataObj.list[i].user_id] = dataObj.list[i];
        li = li + "<li id='inroom_" + dataObj.list[i].user_id + "'>" +
        "<a href=\"javascript:selectUser('"
        + dataObj.list[i].user_id + "')\">" + "<img src='" + dataObj.list[i].avatar
        + "' title='" + dataObj.list[i].nickname + "' width='50' height='50'></a></li>";
        if (dataObj.list[i].user_id != client_user_id) {
            option = option + "<option value='" + dataObj.list[i].user_id + "' id='user_" + dataObj.list[i].user_id + "'>"
                + dataObj.list[i].nickname + "</option>"
        }
    }
    $('#left-userlist').html(li);
    $('#userlist').html(option);
	
}

/**
 * 显示所有历史消息列表
 * @param dataObj
 */
function showChatHistory(dataObj) {
    var msg;
    for (var i = 0; i < dataObj.list.length; i++) {
        msg = dataObj.list[i];
        if (!msg) continue;
        msg['cmd'] = 'chatHistory';
        showNewMsg(msg);
    }
}

/**
 * 显示所有历史弹幕列表
 * @param dataObj
 */
function showBarrageHistory(dataObj) {
    var msg;
    for (var i = 0; i < dataObj.list.length; i++) {
        msg = dataObj.list[i];
        if (!msg) continue;
        msg['cmd'] = 'barrageHistory';
        showNewMsg(msg);
    }
}

/**
 * 当有一个新用户连接上来时
 * @param dataObj
 */
function showNewUser(dataObj) {
    //if (!userlist[dataObj.userInfo.user_id]) {
    //    userlist[dataObj.userInfo.user_id] = dataObj.userInfo;        
    //}	
	if (dataObj.userInfo.user_id != client_user_id) {
		$('#userlist').append("<option value='" + dataObj.userInfo.user_id + "' id='user_" + dataObj.userInfo.user_id + "'>" + dataObj.userInfo.nickname + "</option>");
	}
	$('#left-userlist').append(
		"<li id='inroom_" + dataObj.userInfo.user_id + "'>" +
			'<a href="javascript: selectUser(\'' + dataObj.userInfo.user_id + '\')">' + "<img src='" + dataObj.userInfo.avatar + "' width='50' height='50'></a></li>");
}

/**
 * 显示新消息
 */
function showNewMsg(dataObj) {

    var content;
    if (!dataObj.contentType || dataObj.contentType == 'text') {
        content = xssFilter(dataObj.data);
    }
    else if (dataObj.contentType == 'image') {
        var image = eval('(' + dataObj.data + ')');
        content = '<br /><a href="' + image.url + '" target="_blank"><img src="' + image.thumb + '" /></a>';
    }

    var fromId = dataObj.from;
    var channal = dataObj.channal;

    content = parseXss(content);
    var said = '';
    var time_str;

    if (dataObj.time) {
        time_str = GetDateT(dataObj.time)
    } else {
        time_str = GetDateT()
    }

    $("#msg-template .msg-time").html(time_str);
    if (fromId == 0) {
        $("#msg-template .userpic").html("");
        $("#msg-template .content").html(
            "<span style='color: green'>【系统消息】</span> " + '<img style="width:50px;height:50px" src="' + dataObj.userInfo.avatar + '" />'+ dataObj.userInfo.nickname +content);
    }
    else {
        var html = '';
		if(dataObj.cmd == 'barrage'){
			if(dataObj.talk_by == 'auto'){
				html +="<span style='color: maroon'>【自动弹幕】</span> ";
			}else{
				html +="<span style='color: darkred'>【弹幕消息】</span> ";
			}
		}else if(dataObj.cmd == 'chat'){
			if(dataObj.talk_by == 'auto'){
				html +="<span style='color: red'>【自动群发】</span> ";
			}else{
				html +="<span style='color: blue'>【群发消息】</span> ";	
			}
		}else if(dataObj.cmd == 'come'){
			if(dataObj.talk_by == 'auto'){
				html +="<span style='color: indigo'>【自动上线】</span> ";
			}else{
				html +="<span style='color: purple'>【来了消息】</span> ";	
			}
		}else if(dataObj.cmd == 'notice'){
			if(dataObj.talk_by == 'auto'){
				html +="<span style='color: darkmagenta'>【自动通知】</span> ";
			}else{
				html +="<span style='color: blueviolet'>【通知消息】</span> ";	
			}
		}else if(dataObj.cmd == 'welcome'){
			if(dataObj.talk_by == 'auto'){
				html +="<span style='color: darkviolet'>【自动欢迎】</span> ";
			}else{
				html +="<span style='color: slateblue'>【欢迎消息】</span> ";	
			}
		}else if(dataObj.cmd == 'chatHistory'){
			html +="<span style='color: sienna'>【历史消息】</span> ";
		}else if(dataObj.cmd == 'barrageHistory'){
			html +="<span style='color: salmon'>【历史弹幕】</span> ";
		}else if(dataObj.cmd == 'like'){
			html +="<span style='color: salmon'>【点赞主播】</span> ";
		}
		
        var to = dataObj.to;
        
        //如果说话的是我自己        
		if (client_user_id == fromId) {
			if (to == 0) {
				said = ':';
				
				html += '<span style="color: orange"><a href="javascript:selectUser('
				+ fromId + ')"><img style="width:50px;height:50px" src="' + dataObj.userInfo.avatar + '" />'+dataObj.userInfo.nickname + said;
				html += '</a></span> '
			}
			else if (channal == 1) {
				said = "我悄悄的对" + userlist[to].nickname + "说:";
				html += '<span style="color: orange">' + said + ' </span> ';
			}                
		}
		else {
			if (to == 0) {
				said = ':';
			}
			else if (channal == 1) {
				said = "悄悄的对我说:";
			}

			html += '<span style="color: orange"><a href="javascript:selectUser('
				+ fromId + ')"><img style="width:50px;height:50px" src="' + dataObj.userInfo.avatar + '" />'+dataObj.userInfo.nickname + said;
			html += '</a></span> '
		}
        
        html += content + '</span>';
        $("#msg-template .content").html(html);
    }
    $("#chat-messages").append($("#msg-template").html());
    $('#chat-messages')[0].scrollTop = 1000000;
}

function xssFilter(val) {
    val = val.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\x22/g, '&quot;').replace(/\x27/g, '&#39;');
    return val;
}

function parseXss(val) {
    for (var i = 1; i < 20; i++) {
        val = val.replace('#' + i + '#', '<img src="/live/static/img/face/' + i + '.gif" />');
    }
    val = val.replace('&amp;', '&');
    return val;
}

function GetDateT(time_stamp) {
    var d;
    d = new Date();

    if (time_stamp) {
        d.setTime(time_stamp * 1000);
    }
    var h, i, s;
    h = d.getHours();
    i = d.getMinutes();
    s = d.getSeconds();

    h = ( h < 10 ) ? '0' + h : h;
    i = ( i < 10 ) ? '0' + i : i;
    s = ( s < 10 ) ? '0' + s : s;
    return h + ":" + i + ":" + s;
}

function getRequest() {
    var url = location.search; // 获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);

        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            var decodeParam = decodeURIComponent(strs[i]);
            var param = decodeParam.split("=");
            theRequest[param[0]] = param[1];
        }

    }
    return theRequest;
}

function selectUser(userid) {
    $('#userlist').val(userid);
}

function delUser(userid) {
    $('#user_' + userid).remove();
    $('#inroom_' + userid).remove();
    //delete (userlist[userid]);
}

function online() { 
	// 上线
	msg = new Object();
	msg.cmd = 'login';        
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
	msg.source = user.source;
	msg.channel = user.channel;
	msg.authtype = user.authtype;        
	msg.is_superman = user.is_superman;
		
	ws.send($.toJSON(msg));
}

function offline() {
	//下线    
	msg = new Object();
	msg.cmd = 'offline';	 	
	msg.from = user.user_id;	
	msg.to = '';  
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
	msg.source = user.source;
	msg.channel = user.channel;
	msg.authtype = user.authtype;	
	ws.send($.toJSON(msg));
}

function getOnline() {    
	//获取在线列表
	msg = new Object();
	msg.cmd = 'getOnlineList';        
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;		
    ws.send($.toJSON(msg));
}

function getChatHistory() {
    //获取聊天历史记录
	msg = new Object();
	msg.cmd = 'getChatHistoryList';        
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;		
    ws.send($.toJSON(msg));
}

function getBarrageHistory() {
    //获取弹幕历史记录
	msg = new Object();
	msg.cmd = 'getBarrageHistoryList';        
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;		
    ws.send($.toJSON(msg));
}

function getVersion() {
    //获取版本号
	msg = new Object();
	msg.cmd = 'getVersion';		
    ws.send($.toJSON(msg));
}

function setVip() {
	var user_id = $('#userlist').val();
	alert(user_id);
	
    //设置VIP用户
	msg = new Object();
	msg.cmd = 'setVip'; 	
	msg.from = user.user_id;	
	msg.to = '';       
	msg.room_id = user.room_id;
	msg.user_id = user_id;		
    ws.send($.toJSON(msg));
}

function notice(data) {
    //发送通知
	msg = new Object();
	msg.cmd = 'notice';	
	msg.from = user.user_id;	
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
	msg.data = data;
    ws.send($.toJSON(msg));
	$('#msg_content').val('')
}

function welcome(num) {
    //发送欢迎
	msg = new Object();
	msg.cmd = 'welcome';	
	msg.from = user.user_id;	
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
	msg.num = num;
    ws.send($.toJSON(msg));
}


function like() {
    //点赞主播
	msg = new Object();
	msg.cmd = 'like';
	msg.from = user.user_id;
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
    ws.send($.toJSON(msg));
}


function closeLive() {
    //关闭直播
	msg = new Object();
	msg.cmd = 'closeLive';	
	msg.from = user.user_id;	
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
    ws.send($.toJSON(msg));
}

function pauseLive() {
    //暂停直播
	msg = new Object();
	msg.cmd = 'pauseLive';	
	msg.from = user.user_id;	
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
    ws.send($.toJSON(msg));
}

function resumeLive() {
    //继续直播
	msg = new Object();
	msg.cmd = 'resumeLive';	
	msg.from = user.user_id;	
	msg.to = '';
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;
    ws.send($.toJSON(msg));
}

function sendMsg(content, cmd, type) {
    var msg = {};

    if (typeof content == "string") {
        content = content.replace(" ", "&nbsp;");
    }

    if (!content) {
        return false;
    }
		
    //if ($('#userlist').val() == 0) {
    //  msg.channal = 0;
    //}
    //else {
	//	msg.channal = 1;        
    //}
	
	msg.cmd = cmd;
	msg.from = client_user_id;
	msg.to = $('#userlist').val();	
	msg.data = content;
	msg.contentType = type;
	msg.room_id = user.room_id;
	msg.user_id = user.user_id;		
	msg.source = user.source;
	msg.channel = user.channel;
	msg.authtype = user.authtype;
	ws.send($.toJSON(msg));
		
    //showNewMsg(msg);
    $('#msg_content').val('')
}

$(document).ready(function () {
    var a = '';
    for (var i = 1; i < 20; i++) {
        a = a + '<a class="face" href="#" onclick="selectFace(' + i + ');return false;"><img src="/live/static/img/face/' + i + '.gif" /></a>';
    }
    $("#show_face").html(a);
});

(function ($) {
    $.fn.extend({
        insertAtCaret: function (myValue) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else if ($t.selectionStart || $t.selectionStart == '0') {

                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
            }
            else {

                this.value += myValue;
                this.focus();
            }
        }
    })
})(jQuery);

function selectFace(id) {
    var img = '<img src="/live/static/img/face/' + id + '.gif" />';
    $("#msg_content").insertAtCaret('#' + id + '#');
    closeChatFace();
}

function showChatFace() {
    $("#chat_face").attr("class", "chat_face chat_face_hover");
    $("#show_face").attr("class", "show_face show_face_hovers");
}

function closeChatFace() {
    $("#chat_face").attr("class", "chat_face");
    $("#show_face").attr("class", "show_face");
}

function toggleFace() {
    $("#chat_face").toggleClass("chat_face_hover");
    $("#show_face").toggleClass("show_face_hovers");
}

