<?php
//I am ofcourse assuming that rename_function would work the same way... I don't have access to it, so I don't know
if (!function_exists('rename_function')) {
    function rename_function($function_name, $alias_name) {
        static $renamed = array();
        if (function_exists($alias_name)){
            return false;
        } elseif(function_exists('__'.$function_name)){
            $function_name = '__'.$function_name;
        }
        if(isset($renamed[$function_name]) && $renamed[$function_name] === $alias_name){
            return true;
        }
        $renamed[$function_name] = $alias_name;
        $rf = new ReflectionFunction($function_name);
        $fproto = $alias_name . '(';
        $fcall = $function_name . '(';
        $need_comma = false;
        foreach ($rf->getParameters() as $param) {
            if ($need_comma) {
                $fproto .= ',';
                $fcall .= ',';
            }
            if($param->isPassedByReference()){
                $fproto .= '&';
                $fcall .= '&';
            }
            $fproto .= '$' . $param->getName();
            $fcall .= '$' . $param->getName();
            if ($param->isOptional() && $param->isDefaultValueAvailable()) {
                $val = $param->getDefaultValue();
                if (is_string($val))
                    $val = "'$val'";
                $fproto .= ' = ' . $val;
            }
            $need_comma = true;
            
        }
        $fproto .= ')';
        $fcall .= ')';

        $f = "function $fproto" . PHP_EOL;
        $f .= '{return ' . $fcall . ';}';
        
        eval($f);
        return true;
    }
}

// die isn't really a function yo
function __die($message=''){
    return die($message);
}

$funcs = array(
    'array_shift'=>'array_remove_first_element',
    'print_r'=>'print_readable',
    'die'=>'cheerio',
    'str_*'=>'string_*',
    'is_int'=>'is_integer',
    'var_dump'=>'variable_dump',
    'preg_*'=>'perl_regular_expression_*',
    'json_*'=>'javascript_object_notation_*',
    'mysql_*'=>'my_structured_query_language_*',
    'mysqli_*'=>'my_structured_query_language_improved_*',
    
);

$defined = get_defined_functions();

foreach($funcs as $old=>$new){
    if(strpos($old, '*') !== FALSE){
        $old_funcs = array();
        $old = str_replace('*', '', $old);
        $new = str_replace('*', '', $new);
        
        foreach($defined['internal'] as $d){
            if(strpos($d, $old) === 0){
                $new_func = str_replace($old, $new, $d);
                rename_function($d, $new_func);
            }
        }
    } else {
        rename_function($old, $new);
    }
}
