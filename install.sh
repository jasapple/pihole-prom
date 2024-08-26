#! /bin/sh 

cp source/metrics.php /var/www/html/admin/
/usr/bin/patch -u /var/www/html/admin/scripts/pi-hole/php/savesettings.php -i patches/savesettings.patch

/usr/bin/patch -u /var/www/html/admin/settings.php -i patches/settings.patch

/usr/bin/patch -u /opt/pihole/webpage.sh -i patches/webpage.patch
