# OneFox

## 简介
OneFox是一个简洁的PHP框架，使用非常方便，简单阅读使用手册即可快速开发自己的网站了。而且OneFox具备以下优点：
* 框架核心不臃肿，加载速度快 
* 更适合api之类的接口业务 
* 模板不依赖模板引擎，减少学习模板语言的成本 
* 核心代码简洁，可根据业务需要调整 

## 安装和配置

### 安装
```
$ git clone https://github.com/zer0131/OneFox.git /home/project
```
> 当然，你也可以自定义其他克隆目录

### nginx配置示例
```
server {
    listen  80;
    server_name  www.appryan.com;
    index index.php index.html index.html;
    root /home/project/app/Public;
    location / {
        try_files $uri $uri/ /index.php?/$uri;
    }
    location ~ .*\.(php|php5)?$ {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    #图片缓存时间设置
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        #expires 30d;
    }
    #JS和CSS缓存时间设置
    location ~ .*\.(js|css)?$ {
        #expires 1h;
    }
    access_log  /usr/local/nginx/logs/OneFox.log;
}
```

### apache配置示例
```
<VirtualHost 80>
    DocumentRoot "/home/project/app/Public"
    ServerName www.appryan.com
    ServerAlias www.appryan.com
    ErrorLog "logs/OneFox.error.log"
    CustomLog "logs/OneFox.access.log" common
</VirtualHost>
```

.htaccess配置
```
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

>  注意：注释的部分可根据实际情况修改

## 目录结构

project  WEB部署目录（或者子目录） 
\├\─README.md       README文件 
\├\─app             应用目录 
\└\─OneFox          框架目录 
