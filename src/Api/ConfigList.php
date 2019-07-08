<?php

namespace Sunnysideup\ConfigManager\Api;

use ReflectionClass;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;

class ConfigList
{
    use Extensible;
    use Injectable;
    use Configurable;

    protected $locationIncludes = [];

    protected $classNameIncludes = [];

    private static $do_not_show = [
        'extra_methods',
        'built_in_methods',
        // 'db',
        // 'has_one',
        // 'has_many',
        // 'many_many',
        // 'belongs_many_many',
        // 'many_many_extraFields',
        // 'belongs',
        // 'field_labels',
        // 'searchable_fields',
        // 'defaults',
        // 'casting',
        // 'indexes',
        // 'summary_fields',
        // 'singular_name',
        // 'plural_name',
        // 'allowed_actions',
        // 'api_access',
        // 'validation_enabled',
        // 'cache_has_own_table',
        // 'fixed_fields',
        // 'classname_spec_cache',
        // 'subclass_access',
        // 'create_table_options',
        // 'default_records',
        // 'belongs_to',
        // 'many_many_extraFields',
        // 'default_sort',
    ];

    public function setLocationIncludes($a)
    {
        $this->locationIncludes = $a;

        return $this;
    }

    public function setClassNameIncludes($a)
    {
        $this->classNameIncludes = $a;

        return $this;
    }

    /**
     * @return array
     */
    public function getListOfConfigs()
    {
        $resultArray = [];

        $doNotShow = $this->Config()->get('do_not_show');

        $classes = $this->configurableClasses();
        foreach ($classes as $class) {
            $reflector = new ReflectionClass($class);
            $fileName = $reflector->getFileName();
            $fileName = str_replace(Director::baseFolder(), '', $fileName);
            $statics = $reflector->getStaticProperties();
            $staticList = [];
            $staticListDefaultOnes = [];
            $staticListCachingStatics = [];
            foreach (array_keys($statics) as $key) {
                if (in_array($key, $doNotShow, true)) {
                    $staticListDefaultOnes[$key] = $key;
                } elseif (substr($key, 0, 1) === '_' || strpos($key, 'cache') !== false) {
                    $staticListCachingStatics[$key] = $key;
                } else {
                    $staticList[$key] = $key;
                }
            }
            $vendor = 'n/a';
            $package = 'n/a';
            $classNameArray = explode('\\', $class);
            if (count($classNameArray) > 1) {
                $vendor = $classNameArray[0];
                $package = $classNameArray[1];
            }
            $lists = [
                'static' => $staticList,
                'default' => $staticListDefaultOnes,
                'caching' => $staticListCachingStatics
            ];
            $shortClassName = ClassInfo::shortName($class);
            $ancestry = ClassInfo::ancestry($class);
            foreach($lists as $type => $list) {
                $isConfigOne = $type === 'static';
                $isDefaultOne = $type === 'default';
                $isCachingOne = $type === 'caching';
                if (count($list)) {
                    foreach($list as $static) {
                        $key = str_replace('/', '-', $fileName.'-'.$static);
                        $resultArray[$key] = [
                            'Vendor' => $vendor,
                            'Package' => $package,
                            'ClassName' => $class,
                            'ShortClassName' => $shortClassName,
                            'FileLocation' => $fileName,
                            'ParentClasses' => $ancestry,
                            'Property' => $static,
                            'IsConfigOne' => $isConfigOne,
                            'IsDefaultOne' => $isDefaultOne,
                            'IsCachingOne' => $isCachingOne,
                            'IsSet' => true,
                            'IsInherited' => false,
                        ];
                    }
                }
            }
        }

        return $resultArray;
    }

    protected function configurableClasses()
    {
        $definedClasses = ClassInfo::allClasses();
        return array_filter(
            $definedClasses,
            function ($className) {
                if (class_exists($className)) {
                    $autoload = true;
                    $traits = [];
                    $class = $className;
                    // Get traits of all parent classes
                    do {
                        $traits = array_merge(class_uses($class, $autoload), $traits);
                        $class = get_parent_class($class);
                    } while ($class);

                    // Get traits of all parent traits
                    $traitsToSearch = $traits;
                    while (! empty($traitsToSearch)) {
                        $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
                        $traits = array_merge($newTraits, $traits);
                        $traitsToSearch = array_merge($newTraits, $traitsToSearch);
                    }

                    foreach (array_keys($traits) as $trait) {
                        $traits = array_merge(class_uses($trait, $autoload), $traits);
                    }
                    return isset($traits[Configurable::class]);
                }
            }
        );
    }
}
