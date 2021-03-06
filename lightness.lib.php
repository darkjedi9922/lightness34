<?php namespace lightlib;

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

/**
 * Кодирует спецсимволы в $data.
 * @param string|array $data Если передан массив, то функция будет вызвана рекурсивно
 * для каждого элемента массива. Если значение массива является массивом, то для него
 * тоже будет вызвана эта функция и т.д.
 * @return string|array
 */
function encode_specials($data)
{
    if (is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) $result[$key] = encode_specials($value);
        return $result;
    }

    return htmlspecialchars($data, ENT_QUOTES);
}

/**
 * Противоположно encode_specials().
 * @see encode_specials()
 */
function decode_specials($data)
{
    if (is_array($data)) {
        $result = [];
        foreach ($data as $key => $value) $result[$key] = encode_specials($value);
        return $result;
    }

    return htmlspecialchars_decode($data, ENT_QUOTES);
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

/**
 * Стирает содержимое со всех уровней output buffer'а,
 * выходя на самый первый
 */
function ob_restart_all()
{
    while (ob_get_level() > 0) ob_end_clean();
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

/**
 * Работает как empty(), но если значение является массивом, то рекурсивно проводит
 * ту же проверку на всех детей (и если они массивы на их детей и т.д.).
 * 
 * @param mixed $value
 */
function empty_recursive($value)
{
    if (is_array($value)) {
        foreach ($value as $v) {
            if (!empty_recursive($v)) return false;
        }
        return true;
    }

    return empty($value);
}

/**
 * Получает значение многомерного массива.
 * 
 * @param array $array Сам массив.
 * @param mixed|array $key Индекс/ключ значения. Если значение вложенное, то
 * указывается массив ключей, ведущими к значению. Например, если массив [1, [2, 3]], 
 * чтобы получить второе значение второго элемента, $key = [1, 1].
 * @return mixed
 */
function array_get_value($array, $key)
{
    if (!is_array($key)) $key = [$key];
    
    $result = &$array;
    for ($i = 0, $c = count($key); $i < $c; ++$i) {
        $nextKey = $key[$i];
        $result = &$result[$nextKey];
    }

    return $result;
}

/**
 * Устанавливает значение многомерного массива.
 * 
 * @param array $array Сам массив.
 * @param mixed|array $key Индекс/ключ значения. Если значение вложенное, то
 * указывается массив ключей, ведущими к значению. Например, если массив [1, [2, 3]], 
 * чтобы установить второе значение второго элемента, $key = [1, 1].
 * @return array
 */
function array_set_value($array, $key, $value)
{
    if (!is_array($key)) $key = [$key];

    $elem = &$array;
    for ($i = 0, $c = count($key) - 1; $i < $c; ++$i) {
        $nextKey = $key[$i];
        if (!isset($array[$nextKey])) $array[$nextKey] = [];
        $elem = &$array[$nextKey];
    }

    $elem[last($key)] = $value;
    return $array;
}

/**
 * isset значения многомерного массива.
 * 
 * @param array $array Сам массив.
 * @param mixed|array $key Индекс/ключ значения. Если значение вложенное, то
 * указывается массив ключей, ведущими к значению. Например, если массив [1, [2, 3]], 
 * чтобы узнать, существует ли второе значение второго элемента, $key = [1, 1].
 * @return array
 */
function array_isset_value($array, $key)
{
    if (!is_array($key)) $key = [$key];

    $elem = &$array;
    for ($i = 0, $c = count($key); $i < $c; ++$i) {
        $nextKey = $key[$i];
        if (!isset($elem[$nextKey])) return false;
        $elem = &$array[$nextKey];
    }

    return true;
}

function stored(array &$storage, string $key, callable $creator) 
{
    return $storage[$key] ?? $storage[$key] = $creator();
}

/**
 * Example:
 * input:   "public/favicon.ico"
 * output:  "/public/favicon.ico?v=532532557"
 * 
 * @throws Exception if there is no such file.
 */
function versionify(string $filename): string
{
    if (!file_exists($filename)) throw new \Exception("There is no file $filename");
    $version = filemtime($filename);
    return "/$filename?v=$version";
}


/**
 * @param string    $str           Original string
 * @param string    $needle        String to trim from the beginning of $str
 * @return string Trimmed string
 * @link https://stackoverflow.com/a/32739088/12577122
 */
function remove_prefix(string $str, string $needle): string
{
    if (strpos($str, $needle) === 0)
        return substr($str, strlen($needle));
    return $str;
}

/**
 * @param string    $str           Original string
 * @param string    $needle        String to trim from the end of $str
 * @return string Trimmed string
 * @link https://stackoverflow.com/a/32739088/12577122
 */
function remove_suffix(string $str, string $needle): string
{
    if (strpos($str, $needle, strlen($str) - strlen($needle)) !== false)
        return substr($str, 0, -strlen($needle));
    return $str;
}

function array_map_assoc(callable $callback, array $array): array
{
    $result = [];
    foreach ($array as $key => $value) $result[] = $callback($key, $value);
    return $result;
}