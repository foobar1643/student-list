# Routing
This application uses Front Controller pattern to process HTTP requests. Therefore, all requests should be recieved by single PHP file. The instructions here will explain how to tell your web server to send HTTP requests to your PHP front controller file.

### Built-in PHP web server
Navigate your terminal to the `/public` directory and run the following command to start a local web server.
```
php -S  localhost:8000 -f index.php
```

### Nginx
This is an example nginx virtual host configuration file. It listens for HTTP connections on port 80. This example assumes that you're running PHP-FPM on port 9000. You should update `root` `server_name` `error_log` and `access_log` to your own values.
```
server {
    listen 80;
    server_name student-list.local;

    error_log /var/log/nginx/students.error.log;
    access_log /var/log/nginx/students.access.log;

    root /var/www/student-list/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri =404;
	    fastcgi_pass 127.0.0.1:9000;
	    fastcgi_intercept_errors on;
	    fastcgi_index index.php;
    	fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
	    fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache HTTP Server
Your .htaccess file in `public` directory should contain the following rules
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
Make sure that `mod_rewrite` is installed with your Apache configuration.
Also make sure that `AllowOverride` is set to `All` in your apache virtual host configuration.

### Lighthttpd
Your lighthttpd configuration should contain the following line
```
url.rewrite-if-not-file = ("(.*)" => "/index.php/$0")
```