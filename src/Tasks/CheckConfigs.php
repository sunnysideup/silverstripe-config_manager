<?php

namespace Sunnysideup\ConfigManager\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use Sunnysideup\ConfigManager\Api\ConfigList;

class CheckConfigs extends BuildTask
{
    protected $title = 'Check configs';

    protected $description = 'Runs through all classes and looks for private statics';

    protected $segment = 'checkconfig';

    protected $enabled = true;

    public function run($request)
    {
        $list = ConfigList();
        $list = $list->getListOfConfigs();
        ksort($list);
        foreach ($list as $values) {
            if (is_array($values['Statics']) && count($values['Statics'])) {
                echo '<h3>' . $values['Name'] . ' (' . implode('/', $values['FileLocation']) . ')</h3><ul>';
                foreach ($values['Statics'] as $name) {
                    echo '<li>' . $name . '</li>';
                }
                echo '
                </ul>';
            }
        }
        DB::alteration_message('<h1>==================================</h1>');
    }
}
