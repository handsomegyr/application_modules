后台管理系统
因为需要上传文件 语音 视频 图片等操作
所以需要放开一些限制

编辑php.ini
upload_max_filesize = 200M
max_file_uploads = 200
post_max_size = 1024M

编辑nginx.ini
client_max_body_size 200M;

重启php和nginx
service nginx restart
service php-fpm restart


目前实现的组件有
001 砍价组件(bargain)
002 投票组件(vote)
003 问卷组件(questionnaire)
004 微信卡券组件(weixincard)
005 活动组件(activity)
006 文章组件(article)
007 邀请组件(invitation)
008 抽奖组件(lottery)
009 签到组件(sign) ？
010 兑换组件(exchange)
011 商品组件(goods)
012 会员组件(member)
013 消息组件(message) ？？
014 订单组件(order)
015 支付组件(payment)
016 积分组件(points)
017 奖品组件(prize)
018 网站组件(site)
019 腾讯设置组件(tencent)
020 微信红包组件(weixinredpack)
021 短信设置组件(sns)
022 省市区组件(area)
023 微信组件(weixin)
024 计划任务组件(cronjob)
025 任务组件(task) ？？
026 邮件设置组件(mail)
027 商店组件(store) ？？
028 运价组件(Freight) ？？


第3方工具
导入优惠券工具(codeimput)--------https://github.com/handsomegyr/codeimput
数据库工具(DBTool)--------https://github.com/handsomegyr/DBTool
