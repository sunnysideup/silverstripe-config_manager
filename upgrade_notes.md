2019-07-07 08:17

# running php upgrade upgrade see: https://github.com/silverstripe/silverstripe-upgrader
cd /var/www/upgrades/upgradeto4
php /var/www/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code upgrade /var/www/upgrades/upgradeto4/config_manager  --root-dir=/var/www/upgrades/upgradeto4 --write -vvv --prompt
Writing changes for 2 files
Running upgrades on "/var/www/upgrades/upgradeto4/config_manager"
[2019-07-07 08:17:02] Applying RenameClasses to ConfigManagerTest.php...
[2019-07-07 08:17:02] Applying ClassToTraitRule to ConfigManagerTest.php...
[2019-07-07 08:17:02] Applying RenameClasses to CheckConfigs.php...
[2019-07-07 08:17:02] Applying ClassToTraitRule to CheckConfigs.php...
[2019-07-07 08:17:02] Applying RenameClasses to _config.php...
[2019-07-07 08:17:02] Applying ClassToTraitRule to _config.php...
modified:	tests/ConfigManagerTest.php
@@ -1,4 +1,6 @@
 <?php
+
+use SilverStripe\Dev\SapphireTest;

 class ConfigManagerTest extends SapphireTest
 {

modified:	src/Tasks/CheckConfigs.php
@@ -2,11 +2,16 @@

 namespace Sunnysideup\ConfigManager\Tasks;

-use BuildTask;
-use ClassInfo;
+
+
 use ReflectionClass;
-use Director;
-use DB;
+
+
+use SilverStripe\Core\ClassInfo;
+use SilverStripe\Control\Director;
+use SilverStripe\ORM\DB;
+use SilverStripe\Dev\BuildTask;
+


 class CheckConfigs extends BuildTask

Writing changes for 2 files
✔✔✔