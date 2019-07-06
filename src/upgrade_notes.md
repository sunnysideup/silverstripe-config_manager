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
# running php upgrade inspect see: https://github.com/silverstripe/silverstripe-upgrader
cd /var/www/upgrades/upgradeto4
php /var/www/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code inspect /var/www/upgrades/upgradeto4/config_manager/src  --root-dir=/var/www/upgrades/upgradeto4/config_manager --write -vvv
Array
(
    [0] => 
    [1] => In IncludedProjectAutoloader.php line 39:
    [2] => 
    [3] =>   [InvalidArgumentException]
    [4] =>   Base path does not have a vendor/autoload.php file available
    [5] => 
    [6] => 
    [7] => Exception trace:
    [8] =>  () at /var/www/upgrader/vendor/silverstripe/upgrader/src/Autoload/IncludedProjectAutoloader.php:39
    [9] =>  SilverStripe\Upgrader\Autoload\IncludedProjectAutoloader->register() at /var/www/upgrader/vendor/silverstripe/upgrader/src/Console/InspectCommand.php:115
    [10] =>  SilverStripe\Upgrader\Console\InspectCommand->enableProjectAutoloading() at /var/www/upgrader/vendor/silverstripe/upgrader/src/Console/InspectCommand.php:64
    [11] =>  SilverStripe\Upgrader\Console\InspectCommand->execute() at /var/www/upgrader/vendor/symfony/console/Command/Command.php:255
    [12] =>  Symfony\Component\Console\Command\Command->run() at /var/www/upgrader/vendor/symfony/console/Application.php:924
    [13] =>  Symfony\Component\Console\Application->doRunCommand() at /var/www/upgrader/vendor/symfony/console/Application.php:273
    [14] =>  Symfony\Component\Console\Application->doRun() at /var/www/upgrader/vendor/symfony/console/Application.php:149
    [15] =>  Symfony\Component\Console\Application->run() at /var/www/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code:55
    [16] => 
    [17] => inspect [-d|--root-dir ROOT-DIR] [-w|--write] [--skip-visibility] [--] <path>
    [18] => 
)

# running php upgrade inspect see: https://github.com/silverstripe/silverstripe-upgrader
cd /var/www/upgrades/upgradeto4
php /var/www/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code inspect /var/www/upgrades/upgradeto4/config_manager/src  --root-dir=/var/www/upgrades/upgradeto4 --write -vvv
Writing changes for 0 files
Running post-upgrade on "/var/www/upgrades/upgradeto4/config_manager/src"
[2019-07-07 08:21:48] Applying ApiChangeWarningsRule to CheckConfigs.php...
[2019-07-07 08:21:48] Applying UpdateVisibilityRule to CheckConfigs.php...
Writing changes for 0 files
✔✔✔