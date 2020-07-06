<?php

namespace Sunnysideup\ConfigManager\Api;

use ReflectionClass;
use SilverStripe\Config\Collections\DeltaConfigCollection;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injectable;

class ConfigList
{
    use Extensible;
    use Injectable;
    use Configurable;

    private static $exceptional_classes = [
        Extension::class,
    ];

    private static $do_not_show = [
        'extra_methods',
        'built_in_methods',
    ];

    // protected $locationIncludes = [];
    //
    // protected $classNameIncludes = [];
    //
    // public function setLocationIncludes($a)
    // {
    //     $this->locationIncludes = $a;
    //
    //     return $this;
    // }
    //
    // public function setClassNameIncludes($a)
    // {
    //     $this->classNameIncludes = $a;
    //
    //     return $this;
    // }

    /**
     * @return array
     */
    public function getListOfConfigs(): array
    {
        $resultArray = [];

        $doNotShow = $this->Config()->get('do_not_show');

        $config = Config::inst();
        $alsoSet = $config->getAll();
        $base = Director::baseFolder();

        $classes = $this->configurableClasses();
        foreach ($classes as $class) {
            $reflector = new ReflectionClass($class);
            $fileName = $reflector->getFileName();
            $fileName = str_replace($base, '', $fileName);

            //lists
            $staticListDelta = $this->getDeltas($config, $class);
            $staticListDynamic = array_keys($alsoSet[strtolower($class)]);
            $defaultLists = $this->getDefaultLists($reflector, $doNotShow);
            $originalValues = $defaultLists['OriginalValues'];

            $lists = [
                //do first so that they will always show up
                'runtime' => $staticListDelta,
                'system' => $defaultLists['Caching'],
                'caching' => $defaultLists['System'],
                'property' => $defaultLists['Property'],
                //needs to be last so that only dynamic ones that are
                //not set as property are included
                'dynamic' => $staticListDynamic,
            ];

            foreach ($lists as $type => $list) {
                foreach ($list as $property) {
                    $key = str_replace('/', '-', $fileName . '-' . $property);
                    if (! isset($resultArray[$key])) {
                        $value = $config->get($class, $property, Config::UNINHERITED);
                        $hasValue = $value ? true : false;
                        $originalValue = isset($originalValues[$property]) ? $originalValues[$property] : '';
                        if (is_object($value)) {
                            $value = 'object';
                        }
                        $isDefault = true;
                        $default = '';
                        if ($originalValue && $originalValue !== $value) {
                            $isDefault = false;
                            if ($value && $originalValue) {
                                $default = $originalValue;
                            }
                        }
                        $hasDefault = $originalValue ? true : false;
                        $resultArray[$key] = array_merge(
                            $this->getClassIntel($class),
                            [
                                'FileLocation' => $fileName,
                                'Property' => $property,
                                'Type' => $type,
                                'IsDefault' => $isDefault,
                                'HasDefault' => $hasDefault,
                                'HasValue' => $hasValue,
                                'Default' => $default,
                                'Value' => $value,
                            ]
                        );
                    }
                }
            }
        }

        return $resultArray;
    }

    /**
     * info about static
     * @param  ReflectionClass $reflector
     * @param  bool $doNotShow
     *
     * @return array
     */
    protected function getDefaultLists($reflector, $doNotShow): array
    {
        //vars
        $staticListSystem = [];
        $staticListCaching = [];
        $staticListProperty = [];
        $originalValues = [];
        //start loop
        $statics = $reflector->getStaticProperties();
        foreach (array_keys($statics) as $property) {
            $propertyObject = $reflector->getProperty($property);
            if ($propertyObject->isPrivate()) {
                $propertyObject->setAccessible(true);
                $originalValues[$property] = $propertyObject->getValue($reflector);

                if (in_array($property, $doNotShow, true)) {
                    $staticListSystem[$property] = $property;
                } elseif (substr($property, 0, 1) === '_' ||
                    strpos($property, 'cache') !== false
                ) {
                    $staticListCaching[$property] = $property;
                } else {
                    $staticListProperty[$property] = $property;
                }
            }
        }
        return [
            'System' => $staticListSystem,
            'Caching' => $staticListCaching,
            'Property' => $staticListProperty,
            'OriginalValues' => $originalValues,
        ];
    }

    /**
     * info about class
     * @param  string $class
     *
     * @return array
     */
    protected function getClassIntel(string $class): array
    {
        $vendor = 'n/a';
        $package = 'n/a';
        $shorterClassname = $class;
        $classNameArray = explode('\\', $class);
        $shortClassName = ClassInfo::shortName($class);
        $ancestry = ClassInfo::ancestry($class);
        $childClasses = ClassInfo::subclassesFor($class, false);
        if (count($classNameArray) > 1) {
            $vendor = $classNameArray[0];
            $package = $classNameArray[1];
            array_shift($classNameArray);
            array_shift($classNameArray);
            $shorterClassname = implode(' / ', $classNameArray);
        }

        return [
            'Vendor' => $vendor,
            'Package' => $package,
            'ClassName' => $class,
            'ShorterClassName' => $shorterClassname,
            'ShortClassName' => $shortClassName,
            'ParentClasses' => $ancestry,
            'ChildClasses' => $childClasses,
        ];
    }

    /**
     * get values set at run time (deltas / changed ones)
     *
     * @param  Config $config
     * @param  string $className
     * @return array
     */
    protected function getDeltas($config, $className): array
    {
        $deltaList = [];
        if ($config instanceof DeltaConfigCollection) {
            $deltas = $config->getDeltas($className);
            if (count($deltas)) {
                foreach ($deltas as $deltaInners) {
                    if (isset($deltaInners['config'])) {
                        $deltaList = array_merge(
                            $deltaList,
                            array_keys($deltaInners['config'])
                        );
                    }
                }
            }
        }

        return $deltaList;
    }

    /**
     * get a list of class with the Configurable Trait.
     * @return array
     */
    protected function configurableClasses(): array
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
                        if (in_array($class, $this->Config()->get('exceptional_classes'), true)) {
                            $traits[Configurable::class] = Configurable::class;
                        }
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
