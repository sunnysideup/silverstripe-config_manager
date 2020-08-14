<?php

namespace Sunnysideup\ConfigManager\Control;

use SilverStripe\Security\Permission;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
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
        'index' => 'ADMIN',
    ];

    public function Title()
    {
        return $this->title;
    }

    public function index($request)
    {
        if(! Permission::check('Admin')) {
            return Security::permissionFailure($this);
        }
        if (class_exists(\Sunnysideup\WebpackRequirementsBackend\View\RequirementsBackendForWebpack::class, true)) {
            Config::modify()->set(
                \Sunnysideup\WebpackRequirementsBackend\View\RequirementsBackendForWebpack::class,
                'enabled',
                false
            );
        }
        TableFilterSortAPI::include_requirements(
            $tableSelector = '.tfs-holder',
            $blockArray = [],
            $jqueryLocation = 'https://code.jquery.com/jquery-3.4.1.min.js',
            $includeInPage = true,
            $jsSettings = [
                'rowRawData' => $this->Data(),
                'scrollToTopAtPageOpening' => true,
                'sizeOfFixedHeader' => 0,
                'maximumNumberOfFilterOptions' => 20,
                'filtersParentPageID' => '',
                'favouritesParentPageID' => '',
                'visibleRowCount' => 20,
                'startWithOpenFilter' => true,
                'dataDictionary' => [
                    'Type' => [
                        'Label' => 'Type',
                        'CanFilter' => true,
                    ],
                    'Vendor' => [
                        'Label' => 'Vendor',
                        'CanFilter' => true,
                    ],
                    'Package' => [
                        'Label' => 'Package',
                        'CanFilter' => true,
                    ],
                    'ShorterClassName' => [
                        'Label' => 'Class',
                        'CanFilter' => true,
                    ],
                    'Property' => [
                        'Label' => 'Property',
                        'CanFilter' => true,
                    ],
                    'IsDefault' => [
                        'Label' => 'Is Default',
                        'CanFilter' => true,
                    ],
                    'HasDefault' => [
                        'Label' => 'Has Default',
                        'CanFilter' => false,
                    ],
                    'HasValue' => [
                        'Label' => 'Has Value',
                        'CanFilter' => true,
                    ],
                    'Value' => [
                        'Label' => 'Value',
                        'CanFilter' => false,
                    ],
                    'Default' => [
                        'Label' => 'Default Value',
                        'CanFilter' => false,
                    ],
                ],
            ]
        );
        return $this->renderWith('Sunnysideup/ConfigManager/Control/CheckConfigsTable');
    }

    public function Data()
    {
        $list = new ConfigList();
        $list = $list->getListOfConfigs();
        ksort($list);
        $finalArray = [];
        $count = 0;
        foreach ($list as $values) {
            $count++;
            $id = 'row' . $count;
            $finalArray[$id] = [];
            $finalArray[$id]['Vendor'] = $values['Vendor'];
            $finalArray[$id]['Package'] = $values['Package'];
            $finalArray[$id]['ShorterClassName'] = $values['ShorterClassName'];
            $finalArray[$id]['Property'] = $values['Property'];
            $finalArray[$id]['Type'] = $values['Type'];
            $finalArray[$id]['IsDefault'] = $values['IsDefault'] ? 'yes' : 'no';
            $finalArray[$id]['HasDefault'] = $values['HasDefault'] ? 'yes' : 'no';
            $finalArray[$id]['HasValue'] = $values['HasValue'] ? 'yes' : 'no';
            $finalArray[$id]['Value'] = '<pre>' . print_r($values['Value'], 1) . '</pre>';
            $finalArray[$id]['Default'] = '<pre>' . print_r($values['Default'], 1) . '</pre>';
        }

        return $finalArray;
    }
}
