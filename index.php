<?php

require 'british_config.php';

echo '<pre>';
$functions = get_defined_functions();
array_shift($functions['user']);
array_shift($functions['user']);
echo print_readable($functions['user'], 1);
echo '</pre>';

cheerio('pip pip');