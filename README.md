# pihole-prom

Adding a prometheus metrics exporter directly to pihole's web interface.

Based off pi-hole/pi-hole [v5.18.3](https://github.com/pi-hole/pi-hole/releases/tag/v5.18.3) and pi-hole/web [v5.21](https://github.com/pi-hole/web/releases/tag/v5.21)

## Installation

`sudo ./instal.sh` This will install a patch to the following:

 - /var/www/html/admin/
    - metrics.php
    - settings.php to
- var/www/html/admin/scripts/pi-hole/php
    - savesettings.php 
- /opt/pihole/
    - webpage.sh


## Prometheus Scrape config

```
- job_name: pihole
  metrics_path: "/admin/metrics.php"
  static_configs:
    - targets:
    - 192.168.0.53 # IP of pihole
```


## Further Reading

https://jasapple.com/pihole-prometheus/