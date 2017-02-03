/**
 * @file 公共JS文件
 * @author Mayon
 */
(function(){
    var domain = 'http://yoox.rice5.com.cn';

    /**
     * 微信分享
     */
    function wxShareInit(config) {
        //通过config接口注入权限验证配置
        wx.config({
            debug: false,            // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: config.appId,              // 必填，公众号的唯一标识
            timestamp: config.timestamp,          // 必填，生成签名的时间戳
            nonceStr: config.nonceStr,           // 必填，生成签名的随机串
            signature: config.signature,          // 必填，签名，见附录1
            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone']
                                    // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
        //通过ready接口处理成功验证
        wx.ready(function(){
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
            var shareInfo = {
                title: '【新年时尚柜礼】开启一份来自意大利YOOX的邀请',  // 分享标题
                desc: '米兰来回双人机票，折上折优惠劵，即刻解锁您的专属福利！',   // 分享描述
                link: domain + '/cny/index.html',       // 分享链接
                imgUrl: domain + '/cny/img/share.jpg',  // 分享图标
                type: 'link',           // 分享类型,music、video或link，不填默认为link
                dataUrl: '',            // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {  // 用户确认分享后执行的回调函数
                },
                cancel: function () {   // 用户取消分享后执行的回调函数
                }
            };
            // 分享到朋友圈
            wx.onMenuShareTimeline(shareInfo);
            // 分享给朋友
            wx.onMenuShareAppMessage(shareInfo);
            // 分享到QQ
            wx.onMenuShareQQ(shareInfo);
            // 分享到腾讯微博
            wx.onMenuShareWeibo(shareInfo);
            // 分享到QQ空间
            wx.onMenuShareQZone(shareInfo);
        });
        //通过error接口处理失败验证
        wx.error(function(res){
            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
            console.info(res);
        });
    }

    // 长按
    $.fn.longPress = function(fn) {
        var timeout = undefined;
        var $this = this;
        for(var i = 0;i<$this.length;i++){
            $this[i].addEventListener('touchstart', function(event) {
                timeout = setTimeout(fn, 800);
                }, false);
            $this[i].addEventListener('touchend', function(event) {
                clearTimeout(timeout);
                }, false);
        }
    }

    // 复制
    function setText(text){
    　　var text = text || '';
    　　window.clipboardData.setData('text', text);
　　}

    function randomBg() {
        var sum = 6;
        var index = parseInt(Math.random() * sum);
        var $page2Bg = $('.page2-bg');
        $page2Bg.css('background-image', 'url("img/page2-bg' + index + '.jpg")');
    }

    function handSlide() {
        var $hand = $('.page1-hand');
        var index = 0;
        var site = [[0,0],[0,1],[0,2],[1,0],[1,2]];

        var timer = setInterval(function () {
            if (index >= site.length) {
                index = 0;
            }
            $hand.css({
                top: (16 + site[index][0] * 20) + '%',
                left: (14 + site[index][1] * 28) + '%'
            });
            $hand.addClass('click');
            setTimeout(function () {
                $hand.removeClass('click');
            },800);
            index++;
        }, 1200);
    }

    function slideToPage(index) {
        var _index = index || 1;
        var $pages = $('#J_pages');
        $pages.data('index', _index);
        if (_index == 1) {
            setTimeout(function () {
                handSlide();
            }, 2000);
        } else if (_index == 2) {
            // 手指动画复原
            var $hand = $('.page1-hand');
            $hand.css({
                top: (16) + '%',
                left: (14) + '%'
            });
            clearInterval();
            // 显示随机背景
            randomBg();
        }
    }

    function shakeInput(input) {
        var className = 'shake';
        if (input.hasClass(className)) {
            input.removeClass(className);
        }
        clearTimeout();
        input.addClass('shake');
        setTimeout(function () {
            input.removeClass('shake');
        }, 420);
    }

    function closeAlert() {
        $('.alert').removeClass('open').css('visibility', 'hidden');
    }

    function openAlert(id) {
        closeAlert();
        var $cur_alert = $('#' + id);
        console.info($cur_alert);
        $cur_alert.css('visibility', 'visible').addClass('open');
    }

    function showResult() {
        var $result;
        if (loterryInfo.prize_info.is_virtual) {
            $result = $('#J_alertResult2');
            $result.find('.gift-name').html(loterryInfo.prize_info.prize_name);
            $result.find('input[name="code"]').val(loterryInfo.code_info.code);
            openAlert('J_alertResult2');            // 虚拟奖品
        } else {
            $result = $('#J_alertResult1');
            console.info($result.find('.gift-name'));
            $result.find('.gift-name').html(loterryInfo.prize_info.prize_name);
            openAlert('J_alertResult1');            // 实物奖品
        }
    }

    function lottery() {
        var $codeInput = $('#J_codeInput');
        var code = $codeInput.val();
        if (!code) {
            shakeInput($codeInput);
            return false;
        } else if ( code.toUpperCase() != 'YOOX' ) {
            $codeInput.val('请输入 YOOX !');
            shakeInput($codeInput);
            return false;
        }
        // 流程更改为只要输入一次 yoox 就抽奖一次
        // if (user.userInfo.is_got_prize) {   // 如果已经中过奖
        //     if (user.userInfo.is_record_lottery_user_contact_info) {    // 如果已经领取奖品
        //         alert('你已经领取过奖品,把机会让给他人吧!');
        //         return false;
        //     } else {                                                    // 如果未领取奖品,则直接显示领奖结果页面
        //         loterryInfo = user.userInfo.prizeInfo;
        //         showResult();
        //     }
        //     return false;
        // }
        $.ajax({
            url : domain + '/campaign/cny/lottery',
            type : 'GET',
            data : {},
            dataType : "json",
            contentType: "application/json",
            success : function (res) {
                console.info(res);
                if (res.success) {
                    loterryInfo = res.result;
                    showResult();
                } else {
                    $codeInput.val(res.error_msg);
                    shakeInput($codeInput);
                }
            },
            error : function (err) {
                console.info(err);
            }
        });
    }

    function showRule() {
        openAlert('J_alertRule');
    }

    function sendPhone() {
        var $result = $('#J_alertResult2');
        var $mobile = $result.find('input[name="mobile"]');

        if (!$mobile.val()) {
            shakeInput($mobile);
            return false;
        }
        var data = {};
        data.exchange_id = loterryInfo.exchange_id;
        data.identity_id = loterryInfo.identity_id;
        data.mobile = $mobile.val();
        if(!/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/.test(data.mobile)) {
            shakeInput($mobile);
            $mobile.val('手机号不正确');
            return false;
        }
        var $btn = $(this);
        $btn.html('发送中').attr('disable','');
        $result.find('.send-loading').show();
        $.ajax({
            url : domain + '/campaign/cny/sendsms',
            type : 'GET',
            data : data,
            dataType : "json",
            contentType: "application/json",
            success : function (res) {
                console.info(res);
                $result.find('.send-loading').hide();
                if (res.success) {
                    //add billy
                    $btn.html('已发送');
                    $mobile.val('短信已发送成功!');
                    $('#J_sendPhone').off('click', sendPhone);
                } else {
                    console.info(res.error_msg);
                    shakeInput($mobile);
                    $mobile.val(res.error_msg);
                    $btn.html('发 送').removeAttr('disable');
                }
            },
            error : function (err) {
                console.info(err);
                shakeInput($mobile);
                $mobile.val('网络有误，重新再试');
                $btn.html('发 送').removeAttr('disable');
                $result.find('.send-loading').hide();
            }
        });
    }

    function sendInfo() {
        var $result = $('#J_alertResult1');
        var $mobile = $result.find('input[name="mobile"]');

        var $inputs = $result.find('input');
        for (var i = 0; i < 3; i++) {
            var $input = $inputs.eq(i);
            if (!$input.val()) {
                shakeInput($input);
                return false;
            }
        }
        var data = {};
        data.exchange_id = loterryInfo.exchange_id;
        data.identity_id = loterryInfo.identity_id;
        data.name = $result.find('input[name="name"]').val();
        data.mobile = $result.find('input[name="mobile"]').val();
        data.address = $result.find('input[name="address"]').val();
        if(!/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/.test(data.mobile)) {
            shakeInput($mobile);
            $mobile.val('手机号不正确');
            return false;
        }
        var $btn = $(this);
        $btn.html('提交中').attr('disable','');
        $result.find('.send-loading').show();
        $.ajax({
            url : domain + '/campaign/cny/recorduserinfo',
            type : 'GET',
            data : data,
            dataType : "json",
            contentType: "application/json",
            success : function (res) {
                console.info(res);
                $result.find('.send-loading').hide();
                if (res.success) {
                    $btn.html('已提交');
                    $('#J_sendInfo').off('click', sendInfo);
                } else {
                    shakeInput($mobile);
                    $mobile.val(res.error_msg);
                    $btn.html('提交信息').removeAttr('disable');
                }
            },
            error : function (err) {
                console.info(err);
                shakeInput($mobile);
                $mobile.val('网络有误，重新再试');
                $btn.html('提交信息').removeAttr('disable');
                $result.find('.send-loading').hide();
            }
        });
    }

    function lotteryAgain() {
        $('input').val('');
        closeAlert();
        slideToPage(1);
    }

    function getUserinfo() {
        $.ajax({
            url : domain + '/campaign/cny/getcampaignuserinfo',
            type : 'POST',
            data : {},
            dataType : "json",
            contentType: "application/json",
            success : function (res) {
                console.info(res);
                if (res.success) {
                    user = res.result;
                    slideToPage(2);
                } else if (res.error_code == -40001 || res.error_code == -40002 || res.error_code == -40003 || res.error_code == -40499) {
                    console.info(res.error_msg);
                } else if (res.error_code == -40498) {
                    location.href = domain + '/campaign/cny/weixinauthorizebefore?callbackUrl='
                        + encodeURIComponent('http://yoox.rice5.com.cn/cny/index.html?show=2');
                }
            },
            error : function (err) {
                console.info(err);
            }
        });
    }

    function checkAuthorize() {
        if (Weixin_userInfo) {  // 如果已经授权过,则拉取用户信息
            getUserinfo();
        } else {                // 如果未授权,则跳转到授权地址
            location.href = domain + '/campaign/cny/weixinauthorizebefore?callbackUrl='
                + encodeURIComponent('http://yoox.rice5.com.cn/cny/index.html?show=2');
        }
    }

    function copy(){
        setText($(this).val());
    }

    function bind() {
        $('.page1-box').on('click', function () {
            checkAuthorize();
        });
        $('.page2-btn').on('click', lottery);
        $('.J_closeAlert').on('click', closeAlert);
        $('#J_ruleTrigger').on('click', showRule);
        $('#J_sendPhone').on('click', sendPhone);
        $('#J_sendInfo').on('click', sendInfo);
        $('.J_lotteryAgain').on('click', lotteryAgain);
        $('#J_code').longPress(copy);
    }

    function init() {
        // 界面初始化
        var $loader = $('#J_loading');
        var $main = $('#J_main');
        if (/show=2/.test(location.href)) {
            $loader.hide();
            $main.removeClass('hide');
            $main.addClass('init');
            bind();
            checkAuthorize();
        } else {
            setTimeout(function () {
                $loader.data('state', 'loading');
                setTimeout(function () {
                    $loader.data('state', '');
                    setTimeout(function () {
                        $loader.hide();
                        $main.removeClass('hide');
                        setTimeout(function () {
                            $main.addClass('init');
                            bind();
                            slideToPage(1);
                        },200);
                    },1000);
                },3000);
            },300);
        }
        // 微信分享
        $.ajax({
            url : domain + '/weixin/index/getjssdkinfo',
            type : 'GET',
            data : {
                url : location.href
            },
            dataType : "json",
            contentType: "application/json",
            success : function (res) {
                console.info(res);
                if (res.success) {
                    wxShareInit(res.result);
                } else {
                    console.info(res.error_msg);
                }
            },
            error : function (err) {
                console.info(err);
            }
        });
    }

    var Weixin_userInfo = $.cookie('Weixin_userInfo');
    var user;
    var loterryInfo;

    init();

})();
