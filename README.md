# php unframework template

Fast and lightweight framework-less template for simple sites. Uses [FastRoute](https://github.com/nikic/FastRoute), [Twig](https://twig.symfony.com/) and [Pimple](https://pimple.symfony.com/).

Demo: https://projects.annimon.com/

## Requirements

php 7.4+


## Install

```bash
composer install
chmod -R 777 cache
cd src && npm install
npm run dist
```

<details>
<summary>Nginx config</summary>

```nginx
server {
  listen 80;
  listen [::]:80;
  listen 443 ssl;

  root /var/www/app/public;
  index index.php index.html index.htm;
  server_name app.site;
  autoindex off;

  location / {
    try_files $uri $uri/ /index.php$is_args$args;
  }
  location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
  }
  location ~* ^.+\.(svg|svgz|eot|otf|woff|jpg|jpeg|gif|png|ico)$ {
    access_log off;
    expires 30d;
  }
}
```
</details>

<details>
<summary>Apache config</summary>

```xml
<VirtualHost *:80>
  ServerName app.site
  ServerAdmin admin@app.site
  DocumentRoot /var/www/app/public

  <Directory /var/www/app>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride None
    Require all granted
  </Directory>
</VirtualHost>
```
</details>


