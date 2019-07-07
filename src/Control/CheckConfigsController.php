<?php

namespace Sunnysideup\ConfigManager\Control;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Control\Controller;
use Sunnysideup\ConfigManager\Api\ConfigList;

class CheckConfigsController extends ContentController
{
    protected $title = 'Check configs';

    protected $description = 'Runs through all classes and looks for private statics';

    protected $url_segment = 'checkconfigs';

    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
        'index' => 'ADMIN'
    ];

    public function index($request)
    {
        $list = new ConfigList();
        $list = $list->getListOfConfigs();
        ksort($list);
        foreach ($list as $values) {
            echo '<h3>' . $values['ShortClassName'] . ' (' . $values['FileLocation'] . ')</h3><ul>';
            foreach ($values['Statics'] as $name) {
                echo '<li>' . $name . '</li>';
            }
            echo '
            </ul>';
        }
        DB::alteration_message('<h1>==================================</h1>');
    }
}
