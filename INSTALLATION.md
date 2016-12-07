#SlimPower - Slim Controller

##Installation

Create folder /var/www/slimpower and download this repository

In terminal:

```sh
mkdir /var/www/slimpower
cd /var/www/slimpower
```

Next, add 'slimpower-slim' repository: 

In terminal:

```sh
composer require matiasnamendola/slimpower-slim
```
Or you can add use this as your composer.json:

```json
{
    "require": {
        "slim/slim": "2.*",
        "matiasnamendola/slimpower-slim": "dev-master"
    }
}
```

###.htaccess

Here's an .htaccess sample for simple RESTful API's

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

###Apache VirtualHost

Create conf file '000-slimpower.conf' in folder '/etc/apache2/sites-available'
with this content:

```conf
<VirtualHost *:80>
        ServerAdmin             webmaster@localhost
        ServerName              dev.slimpower.com.ar
        DocumentRoot            /var/www/slimpower
        ErrorLog               /var/log/apache2/slimpower-custom-error.log
        CustomLog              /var/log/apache2/slimpower-custom.log common
        #TransferLog            /var/log/apache2/slimpower-custom.log
        <Directory /var/www/slimpower/>
                Options -Indexes
                AllowOverride AuthConfig FileInfo
                AddOutputFilterByType DEFLATE text/html
                AddOutputFilterByType DEFLATE text/css
                AddOutputFilterByType DEFLATE application/x-javascript
                AddOutputFilterByType DEFLATE image/gif
        </Directory>
        <files "*.conf">
            order allow,deny
            deny from all
        </files>
        <files "*.ini">
            order allow,deny
            deny from all
        </files>
        <files "*.json">
            order allow,deny
            deny from all
        </files>
        <DirectoryMatch "^/.*/(\.svn|CVS)/">
            Order deny,allow
            Deny from all
        </DirectoryMatch>
</VirtualHost>
```

Next, copy this in terminal:

```sh
sudo a2ensite 000-slimpower
sudo /etc/init.d/apache2 restart
```

or 

```sh
sudo a2ensite 000-slimpower
sudo service apache2 restart
```