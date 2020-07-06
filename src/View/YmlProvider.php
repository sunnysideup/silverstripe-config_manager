<?php
namespace Sunnysideup\ConfigManager\View;

use Sunnysideup\ConfigManager\Api\ConfigList;

use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;
use SilverStripe\ORM\ArrayList;

class YmlProvider extends ViewableData
{

    protected $vendorName = '';
    protected $packageName = '';
    protected $data = [];
    protected $dataAsObject = null;

    public function getYmlForModule(string $vendorName, string $packageName) : string
    {

        $this->vendorName = $vendorName;
        $this->packageName = $packageName;
        $data = (new ConfigList())->getListOfConfigs();
        foreach($data as $key => $item) {
            if(
                strtolower($item['Vendor']) === strtolower($this->vendorName)
                &&
                strtolower($item['Package']) === strtolower($this->packageName)
            ) {
                $this->data[$key] = $item;
            }
        }
        print_r($this->data);
        die('xxx');
        return $this->formatAsYml();
    }

    protected function formatAsYml()
    {
        return $this->renderWith('Sunnysideup/ConfigManager/View/YmlTemplate');
    }

    public function getDataForYmlList()
    {
        $newArray = [];
        foreach($this->data as $item) {
            if(! isset($newArray['Classes'])) {
                $newArray['Classes'] = [];
            }
            if(! isset($newArray['Classes'][$item['ClassName']])) {
                $newArray['Classes'][$item['ClassName']] = [];
                $newArray['Classes'][$item['ClassName']]['Properties'] = [];
            }
            if(! isset($newArray['Classes'][$item['ClassName']]['Properties'][$item['Property']])) {
                $newArray['Classes'][$item['ClassName']]['Properties'][$item['Property']] = [
                    'PropertyName' => $item['Property'],
                    'DefaultValue' => $item['Default'],
                ];
            }
        }
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

    public function getYmlName()
    {
        return $this->vendorName.'_'.$this->moduleName.'_config';
    }



}
