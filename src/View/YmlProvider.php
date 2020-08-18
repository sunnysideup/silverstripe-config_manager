<?php

namespace Sunnysideup\ConfigManager\View;

use SilverStripe\ORM\ArrayList;

use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;
use Sunnysideup\ConfigManager\Api\ConfigList;


use Symfony\Component\Yaml\Yaml;

class YmlProvider extends ViewableData
{
    protected $vendorName = '';

    protected $packageName = '';

    protected $locationFilter = '';

    protected $data = [];

    protected $filteredData = [];

    protected $dataAsObject = null;

    private static $model_fields = [
        'db',
        'has_one',
        'belongs_to',
        'has_many',
        'many_many',
        'many_many_extraFields',
        'belongs_many_many',
    ];

    private static $excluded_properties = [
        'db',
        'casting',
        'has_one',
        'belongs_to',
        'has_many',
        'many_many',
        'many_many_extraFields',
        'belongs_many_many',
        'allowed_actions',
    ];

    public function getYmlForLocation(string $locationFilter): string
    {
        $this->locationFilter = $locationFilter;
        $this->data = (new ConfigList())->getListOfConfigs();
        foreach ($this->data as $key => $item) {
            if (stripos($item['FileLocation'], $locationFilter) !== false) {
                if ($this->itemShouldBeIncluded($item)) {
                    $this->filteredData[$key] = $item;
                }
            }
        }
        return $this->formatAsYml();
    }

    public function getYmlForPackage(string $vendorName, string $packageName): string
    {
        $this->vendorName = $vendorName;
        $this->packageName = $packageName;
        $this->data = (new ConfigList())->getListOfConfigs();
        foreach ($this->data as $key => $item) {
            if (strtolower($item['Vendor']) === strtolower($this->vendorName)
                &&
                strtolower($item['Package']) === strtolower($this->packageName)
            ) {
                if ($this->itemShouldBeIncluded($item)) {
                    $this->filteredData[$key] = $item;
                }
            }
        }
        return $this->formatAsYml();
    }

    public function getModel()
    {
        $this->data = (new ConfigList())->getListOfConfigs();
        foreach ($this->data as $key => $item) {
            if($this->isModelField($item)) {
                $this->filteredData[$key] = $item;
            }
        }
        return $this->formatAsYml();
    }

    public function getDataForYmlList(): ArrayData
    {
        $nestedArray = $this->convertFlatArrayIntoNestedArray($this->filteredData);
        return $this->convertNestedArrayIntoObjects($nestedArray);

        // $this->dataAsObject = new ArrayData(
        //     [
        //         'Classes' => new ArrayList()
        //     ]
        // );
        // $this->dataAsObject->Classes->push(
        //     new ArrayData([
        //         'Properties' => $item['Property'],
        //         'DefaultValue' => '',
        //     ])
        //     'FileLocation' => $fileName,
        //     'Property' => $property,
        //     'Type' => $type,
        //     'IsDefault' => $isDefault,
        //     'HasDefault' => $hasDefault,
        //     'HasValue' => $hasValue,
        //     'Default' => $default,
        //     'Value' => $value,
        // );
    }

    public function getYmlName() : string
    {
        if ($this->locationFilter) {
            $name = strtolower(str_replace('/', '_', $this->locationFilter));
        } else {
            $name = strtolower($this->vendorName . '_' . $this->packageName);
        }
        return $name . '_config_example';
    }

    protected function formatAsYml(): string
    {
        return $this->renderWith('Sunnysideup/ConfigManager/View/YmlTemplate');
    }

    protected function convertFlatArrayIntoNestedArray(array $flatArray): array
    {
        $newArray = [];
        $newArray['Classes'] = [];
        foreach ($flatArray as $item) {
            if (! isset($newArray['Classes'][$item['ClassName']])) {
                $newArray['Classes'][$item['ClassName']] = [];
                $newArray['Classes'][$item['ClassName']]['ClassName'] = $item['ClassName'];
                $newArray['Classes'][$item['ClassName']]['Properties'] = [];
            }
            if (! isset($newArray['Classes'][$item['ClassName']]['Properties'][$item['Property']])) {
                $newArray['Classes'][$item['ClassName']]['Properties'][$item['Property']] = [
                    'PropertyName' => $item['Property'],
                    'DefaultValue' => $this->bestValue($item['Value']),
                ];
            }
        }

        return $newArray;
    }

    protected function convertNestedArrayIntoObjects(array $nestedArray): ArrayData
    {
        $newObject = new ArrayData(
            [
                'Classes' => new ArrayList(),
                'Name' => $this->getYmlName(),
            ]
        );
        foreach ($nestedArray['Classes'] as $properties) {
            $itemHolder = new ArrayData(
                [
                    'ClassName' => $properties['ClassName'],
                    'Properties' => new ArrayList(),
                ]
            );
            foreach ($properties['Properties'] as $propertyData) {
                $itemHolder->Properties->push(new ArrayData($propertyData));
            }
            $newObject->Classes->push($itemHolder);
        }

        return $newObject;
    }

    protected function bestValue($mixed)
    {
        if (is_array($mixed)) {
            $val = "\n" . Yaml::dump($mixed);
            return str_replace("\n", "\n    ", $val);
        }
        if (is_bool($mixed)) {
            if ($mixed) {
                return 'true';
            }
            return 'false';
        }
        if (is_numeric($mixed)) {
            if ($mixed) {
                return $mixed;
            }
            return 0;
        }
        return Yaml::dump($mixed);
    }

    protected function itemShouldBeIncluded(array $item): bool
    {
        return ! in_array($item['Property'], $this->Config()->get('excluded_properties'), true);
    }

    protected function isModelField(array $item): bool
    {
        return in_array($item['Property'], $this->Config()->get('model_fields'), true);
    }
}
