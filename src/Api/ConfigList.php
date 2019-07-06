<?php

namespace Sunnysideup\ConfigManager\Api;

class ConfigList
{
    protected $locationIncludes = [];

    protected $classNameIncludes = [];

    private static $do_not_show = [
        'db',
        'has_one',
        'has_many',
        'many_many',
        'belongs_many_many',
        'many_many_extraFields',
        'belongs',
        'field_labels',
        'searchable_fields',
        'defaults',
        'casting',
        'indexes',
        'summary_fields',
        'singular_name',
        'plural_name',
        'allowed_actions',
        'api_access',
        'validation_enabled',
        'cache_has_own_table',
        'fixed_fields',
        'classname_spec_cache',
        'subclass_access',
        'create_table_options',
        'default_records',
        'belongs_to',
        'many_many_extraFields',
        'default_sort',
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

    public function getListOfConfigs()
    {
        $classes = ClassInfo::implementorsOf(Config::class);
        $doNotShow = $this->Config()->get('do_not_show');
        $resultArray = [];
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
                    continue;
                } elseif (substr($key, 0, 1) === '_') {
                    $staticListCachingStatics[$key] = $key;
                    continue;
                } elseif (strpos($key, 'cache') !== false) {
                    $staticListCachingStatics[$key] = $key;
                    continue;
                }
                $staticList[$key] = $key;
            }
            $resultArray[str_replace('/', '-', $fileName)] = [
                'Vendor' => $class,
                'Name' => $class,
                'FileLocation' => explode('/', $fileName),
                'ParentClasses' => ClassInfo::ancestry($class),
                'Statics' => $staticList,
                'DefaultOnes' => $staticListDefaultOnes,
                'CachedOnes' => $staticListCachingStatics,
            ];
        }
    }
}
