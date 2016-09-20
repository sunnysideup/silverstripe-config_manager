<?php

class CheckConfigs extends BuildTask
{
    protected $title = 'Check configs';

    protected $description = 'Runs through all classes and looks for private statics';

    protected $enabled = true;

    private static $do_not_show = array(
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
        'plural_name'
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
        'default_sort'
    );

    public function run($request)
    {
        $classes = ClassInfo::subclassesFor('Object');
        $doNotShow = $this->Config()->get('do_not_show');
        $resultArray = array();
        foreach($classes as $classKey => $class) {
            $reflector = new ReflectionClass($class);
            $fileName = $reflector->getFileName();
            $fileName = str_replace(Director::baseFolder(), '', $fileName);
            $statics = $reflector->getStaticProperties();
            $staticList = array();
            foreach($statics as $key => $values) {
                if(substr($key, 0, 1) == "_") {
                    continue;
                }
                if(in_array($key, $doNotShow)) {
                    continue;
                }
                if(strpos($key, 'cache') !== false) {
                    continue;
                }
                $staticList[$key] = $key;
            }
            $resultArray[str_replace('/', '-', $fileName)] = array(
                'Name' => $class,
                'FileLocation' => $fileName,
                'Statics' => $staticList
            );
        }
        ksort($resultArray);
        foreach($resultArray as $fileName => $values) {
            if(is_array($values['Statics']) && count($values['Statics'])) {
                echo '<h3>'.$values['Name'].' ('.$values['FileLocation'].')</h3><ul>';
                foreach($values['Statics'] as $name) {
                    echo '<li>'.$name.'</li>';
                }
                echo '
                </ul>';
            }
        }
        DB::alteration_message('<h1>==================================</h1>');
    }
}
