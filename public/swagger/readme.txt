这一步是在其他的网站上显示的时候，需要配置跨域
1 vi /etc/nginx/conf.d/applicationmodule.conf 
location ~ \.php {
        # 跨域相关
        add_header "Access-Control-Allow-Origin" *;
        add_header "Access-Control-Allow-Headers" 'Origin, X-Requested-With, Content-Type, Accept';                       

}
                

1 浏览器请求 http://www.myapplicationmodule.com.com/swagger/swagger-ui/index.html
	在explore输入框输入http://www.myapplicationmodule.com.com/swagger/s1.php?file=SwaggerController.php 点击explore按钮
           在explore输入框输入http://www.myapplicationmodule.com.com/swagger/s1.php?file=Swagger2Controller.php 点击explore按钮

98 通过命令行方式可以生成json文件
	php /learn-php/phalcon/application_modules/vendor/zircote/swagger-php/bin/swagger /learn-php/phalcon/application_modules/public/swagger/api/Examples/ -o /learn-php/phalcon/application_modules/public/swagger/result/
	
99 参考事例
	https://github.com/zircote/swagger-php/blob/master/Examples/petstore.swagger.io
  