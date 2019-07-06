2019-07-07 08:19

# running php upgrade inspect see: https://github.com/silverstripe/silverstripe-upgrader
cd /var/www/upgrades/upgradeto4
php /var/www/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code inspect /var/www/upgrades/upgradeto4/config_manager/src  --root-dir=/var/www/upgrades/upgradeto4 --write -vvv
Writing changes for 0 files
Running post-upgrade on "/var/www/upgrades/upgradeto4/config_manager/src"
[2019-07-07 08:19:02] Applying ApiChangeWarningsRule to CheckConfigs.php...
[2019-07-07 08:19:02] Applying UpdateVisibilityRule to CheckConfigs.php...
Writing changes for 0 files
✔✔✔