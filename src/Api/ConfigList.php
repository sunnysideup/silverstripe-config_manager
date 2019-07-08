<?php

namespace Sunnysideup\ConfigManager\Api;

use ReflectionClass;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
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

        $config = Config::inst();
        $alsoSet = $config->getAll();

        $classes = $this->configurableClasses();
        foreach ($classes as $class) {
            $reflector = new ReflectionClass($class);
            $fileName = $reflector->getFileName();
            $fileName = str_replace(Director::baseFolder(), '', $fileName);
            $statics = $reflector->getStaticProperties();

            //lists
            $deltaList = $this->getDeltas($config, $class);
            $staticList = [];
            $dynamicList = array_keys($alsoSet[strtolower($class)]);
            $staticListDefaultOnes = [];
            $staticListCachingStatics = [];
            foreach (array_keys($statics) as $key) {
                if (in_array($key, $doNotShow, true)) {
                    $staticListDefaultOnes[$key] = $key;
                } elseif (
                    substr($key, 0, 1) === '_' ||
                    strpos($key, 'cache') !== false
                ) {
                    $staticListCachingStatics[$key] = $key;
                } else {
                    $staticList[$key] = $key;
                }
            }
            $vendor = 'n/a';
            $package = 'n/a';
            $shorterClassname = $class;
            $classNameArray = explode('\\', $class);
            if (count($classNameArray) > 1) {
                $vendor = $classNameArray[0];
                $package = $classNameArray[1];
                array_shift($classNameArray);
                array_shift($classNameArray);
                $shorterClassname = implode(' \\ ', $classNameArray);
            }
            $lists = [
                'runtime' => $deltaList,
                'property' => $staticList,
                'caching' => $staticListCachingStatics,
                'system' => $staticListDefaultOnes,
                'dynamic' => $dynamicList,
            ];
            $shortClassName = ClassInfo::shortName($class);
            $ancestry = ClassInfo::ancestry($class);
            foreach($lists as $type => $list) {
                if (count($list)) {
                    foreach($list as $property) {
                        $key = str_replace('/', '-', $fileName.'-'.$property);
                        if(!isset($resultArray[$key])) {
                            $value = $config->get($class, $property, Config::UNINHERITED);
                            $hasValue = $value ? true : false;
                            $originalValue = '';
                            if(is_object($value)) {
                                $value = 'object';
                            } else {
                                if($reflector->hasProperty($property)) {
                                    $propertyObject = $reflector->getProperty($property);
                                    $propertyObject->setAccessible(true);
                                    $originalValue = $propertyObject->getValue($reflector);
                                }
                            }
                            $isDefault = true;
                            $default = '';
                            if($originalValue && $originalValue !== $value) {
                                $isDefault = false;
                                if($value && $originalValue) {
                                    $default = $originalValue;
                                }
                            }
                            $hasDefault = $originalValue ? true : false;
                            $resultArray[$key] = [
                                'Vendor' => $vendor,
                                'Package' => $package,
                                'ClassName' => $class,
                                'ShorterClassName' => $shorterClassname,
                                'ShortClassName' => $shortClassName,
                                'FileLocation' => $fileName,
                                'ParentClasses' => $ancestry,
                                'Property' => $property,
                                'Type' => $type,
                                'IsDefault' => $isDefault,
                                'HasDefault' => $hasDefault,
                                'HasValue' => $hasValue,
                                'Default' => $default,
                                'Value' => $value,
                            ];
                        }
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

    public function getDeltas($config, $className)
    {
        $deltaList = [];
        $deltas = $config->getDeltas($className);
        if(count($deltas)) {
            foreach($deltas as $deltaInners) {
                if(isset($deltaInners['config'])) {
                    $deltaList = array_merge(
                        $deltaList,
                        array_keys($deltaInners['config'])
                    );
                }
            }
        }

        return $deltaList;
    }
}
