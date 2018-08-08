<?php
// Длинну строки вычисляй через mb_strlen() для кириллицы

function session_start_once()
{
	if (!session_id()) session_start();
}

// =============================================================================

function translit(string $s) : string
{
	$s = mb_strtolower($s, 'UTF-8'); // переводим строку в нижний регистр (иногда надо задать локаль)
	$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
	return $s; // возвращаем результат
}

// =============================================================================

/**
 * @throws Exception if file is '' or '/'
 */
function generate_unique_filename(string $file)
{
    if ($file == '' || $file == '/') throw new Exception('The filename of the file "'.$file.'" is empty.');

    for ($i = 1; file_exists($file); ++$i) {
        $pathElements = explode('/', $file);
        $lastIndex = count($pathElements) - 1;
        $nameIndex = ($pathElements[$lastIndex] != '' ? $lastIndex : $lastIndex - 1);
        $nameParts = explode('.', $pathElements[$nameIndex]);

        $nameParts[0] = $nameParts[0].$i;
        $pathElements[$nameIndex] = implode('.', $nameParts);
        $file = implode('/', $pathElements);
    }
    return $file;
}

// =============================================================================

function move_uploaded_unique_file(array $file, string $path, bool $translit = true) : string
{
    $path = rtrim($path, '/');
    $name = ($translit ? translit($file['name']) : $file['name']);
    $uniqueFile = generate_unique_filename($path.'/'.$name);
    if (!file_exists($path)) mkdir($path);
	move_uploaded_file($file['tmp_name'], $uniqueFile);
    return end(explode('/', $uniqueFile));
}

// =============================================================================

function substring(string $str, int $start, int $length = NULL) : string
{
    return mb_substr($str, $start, $length, 'UTF-8');
}

// =============================================================================

function shorten(string $str, int $length) : string
{
	if (mb_strlen($str, 'UTF-8') > $length) return substring($str, 0, $length - 3).'...';
	else return $str;
}

// =============================================================================

function encode_specials(string $text) : string
{
	$text = str_replace("<", "&lt;", $text);
	$text = str_replace(">", "&gt;", $text);
	$text = str_replace("\"", "&quot;", $text);
	$text = str_replace("'", "&#39;", $text);
	return $text;
}

// =============================================================================

function bytes(int $number, string $unit) : int
{
	if ($unit === 'KB') return $number * 1024;
	else if ($unit === 'MB') return $number * 1024 * 1024;
	else if ($unit === 'GB') return $number * 1024 * 1024 * 1024;
	else return $number;
}

// =============================================================================

/**
 * Перебирает по-порядку каждый заданный аргумент (их может быть сколько угодно)
 * и возвращает тот, который в bool равен true. Если ни один, кроме последнего
 * не соответсвует условию, вернет последний аргумент.
 * 
 * Минимальное количество аргументов = 2.
 */
function oneof($val1, $val2)
{
	$args = func_get_args();
	$count = count($args);
	for ($i = 0, $c = $count - 1; $i < $c; ++$i) {
		if ($args[$i]) return $args[$i];
	}
	return $args[$count - 1];
}

// =============================================================================

/**
 * Возвращает последний элемент массива. Если массив пуст, вернет null.
 */
function last(array $arr)
{
    if (empty($arr)) return null;
    return array_pop($arr);
}

// ============================================================================

/**
 * Создает файл (!не папку). Если файл уже существует, не трогает его.
 */
function mkfile(string $file)
{
    if (!file_exists($file)) {
        $handle = fopen($file, 'a');
        fclose($handle);
    }
}

// ============================================================================

/**
 * Собирает одномерный массив в строку.
 */
function array_assemble(array $arr, string $outer_separator, string $inner_separator): string
{
    $pieces = [];
    foreach ($arr as $key => $value) $pieces[] = $key.$inner_separator.$value;
    return implode($outer_separator, $pieces);
}

function show_server_info()
{
    foreach ($_SERVER as $key => $value) {
        echo $key.' = '.$value.endl;
    }
}

function count_file_lines(string $file): int
{
    $handle = fopen($file, "r");
    $i = 0;
    while (!feof($handle)) {
        $bufer = fread($handle, 1048576);
        $i += substr_count($bufer, "\n");
    }
    if (isset($bufer[0]) && $bufer[strlen($bufer) - 1] !== "\n") $i += 1;
    fclose($handle);
    return $i;
}

function array_rename_key(array $array, string $key_old_name, string $key_new_name): array
{
    if (isset($array[$key_old_name]) || array_key_exists($key_old_name, $array)) {
        $array[$key_new_name] = $array[$key_old_name];
        unset($array[$key_old_name]); 
    }
    return $array;
}

function ord_sum(string $str): int
{
    $sum = 0;
    for ($i = 0, $c = strlen($str); $i < $c; ++$i) $sum += ord($str[$i]);
    return $sum;
}

function ob_restart()
{
    if (ob_get_length() != 0) ob_clean();
    ob_start();
}

function http_parse_query($query, $arg_separator)
{
    $query = explode($arg_separator, $query);
    $args = [];
    for ($i = 0, $c = count($query); $i < $c; ++$i) {
        $arg = explode('=', $query[$i]);
        $args[urldecode($arg[0])] = urldecode($arg[1]);
    }
    return $args;
}