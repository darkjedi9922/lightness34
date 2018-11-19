<?php
// Длинну строки вычисляй через mb_strlen() для кириллицы

/**
 * @return bool Была ли запущена сессия
 */
function session_start_once()
{
	if (session_id()) return false;
    return session_start();
}

// =============================================================================

/**
 * @param string $str Строка, которую нужно перевести в транслит
 * @return string
 */
function translit($str)
{
    $pairs = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh',
        'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => '',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'J', 'З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh',
        'Щ' => 'Shch', 'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya', 'Ъ' => '', 'Ь' => '',
    ];
	return strtr($str, $pairs);
}

// =============================================================================

/**
 * Добавляет номер дубликата к имени файла, если такой файл уже существует
 * @param string $file
 * @return string
 */
function generate_unique_filename($file)
{
    $file = trim($file);
    if ($file === '/' || $file === '') return $file;
    while ($file[strlen($file) - 1] === '/') $file = rtrim($file, '/');
    if (!file_exists($file)) return $file;

    $sections = explode('/', $file);
    $lastIndex = count($sections) - 1;
    $nameParts = explode('.', $sections[$lastIndex]);

    $newName = $file;
    $newSections = $nameParts;
    $newPathElements = $sections;

    for ($i = 1; file_exists($newName); ++$i) {
        if (is_file($file)) $newSections[0] = $nameParts[0].'_'.$i;
        else $newSections[count($newSections) - 1] = $nameParts[count($nameParts) - 1].'_'.$i;

        $newPathElements[$lastIndex] = implode('.', $newSections);
        $newName = implode('/', $newPathElements);
    }
    
    return $newName;
}

// =============================================================================

/**
 * Перемещает загруженный файл из $_FILES в директорию, добавляя номер к имени,
 * чтобы оно было уникально (если такой файл уже существует).
 * 
 * Если заданная директория не существует, создает ее.
 * 
 * @param array $file Массив из $_FILES
 * @param string $path Директория, куда нужно переместить файл
 * @param bool $translit Нужно ли конвертировать имя файла в транслит
 * @return string Имя файла после перемещения
 */
function move_uploaded_unique_file($file, $path, $translit = true)
{
    $path = rtrim($path, '/');
    $name = ($translit ? translit($file['name']) : $file['name']);
    $uniqueFile = generate_unique_filename($path.'/'.$name);
    if (!file_exists($path)) mkdir($path);
	move_uploaded_file($file['tmp_name'], $uniqueFile);
    return end(explode('/', $uniqueFile));
}

// =============================================================================

/**
 * Выделяет подстроку из строки.
 * Аналогично substr() с кодировкой UTF-8.
 * 
 * @param string $str
 * @param int $start
 * @param int $length
 * @return string
 */
function substring($str, $start, $length = NULL)
{
    return mb_substr($str, $start, $length, 'UTF-8');
}

// =============================================================================

/**
 * Сокращает строку до заданной длинны. 
 * Если строка была сокращена, добавляет заданное окончание к результату
 * (например, можно использовать '...', для результата в виде 'This i...').
 * 
 * @param string $str
 * @param int $length
 * @param string $ending
 * @return string
 */
function shorten($str, $length, $ending = '')
{
	if (mb_strlen($str, 'UTF-8') > $length) return substring($str, 0, $length).$ending;
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

/**
 * Стирает содержимое со всех уровней output buffer'а,
 * выходя на самый первый
 */
function ob_end_clean_all()
{
    while (ob_get_level() > 0) ob_end_clean();
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