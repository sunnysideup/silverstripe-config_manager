<?php

namespace Sunnysideup\ConfigManager\Control;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Control\Controller;
use SilverStripe\View\ArrayData;
use Sunnysideup\ConfigManager\Api\ConfigList;

use Sunnysideup\TableFilterSort\Api\TableFilterSortAPI;

class CheckConfigsController extends Controller
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

    public function Title()
    {
        return $this->title;
    }

    public function index($request)
    {
        TableFilterSortAPI::include_requirements();

        return $this->renderWith('Includes/CheckConfigsTable');
    }

    public function Data()
    {
        $list = new ConfigList();
        $list = $list->getListOfConfigs();
        ksort($list);
        $finalArray = ArrayList::create();
        foreach ($list as $values) {
            foreach ($values['Statics'] as $name) {
                $innerArray = [
                    'Property' => $name,
                ];
                $tmpValues = $values;
                foreach($tmpValues as $key => $value) {
                    $innerArray[$key] = $value;
                }
            }
            $finalArray[] = $innerArray;
        }

        return $finalArray;
    }

}
