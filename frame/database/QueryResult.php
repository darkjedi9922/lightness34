<?php namespace frame\database;

class QueryResult
{
    /**
     * @var \mysqli_result
     */
    private $result;

    /**
     * @param \mysqli_result $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Возвращает Двумерный массив со всеми массивами-строками из результата.
     * Если в результате нет строк, вернет одномерный пустой массив.
     * @return array
     */
    public function readAll()
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Считывает строку и возвращает ее в виде массива.
     * Если строк больше не осталось, вернет null.
     * @return array|null
     */
    public function readLine()
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    /**
     * Считывает колонку по ее индексу и возвращает в виде одномерного
     * индексного массива.
     * @return array
     */
    public function readColumn($index)
    {
        $all = $this->result->fetch_all(MYSQLI_NUM);
		
		// Нужно превратить ассоциативный массив в обычный
        $result = [];
        for ($i = 0, $c = count($all); $i < $c; ++$i) { // проходимся по строкам
            $result[] = $all[$i][$index]; // записываем значения нужной колонки в массив
        }

        return $result; // а затем возвращаем его
    }

    /**
     * Считывает и возвращает первое значение в текущей строке. 
     * Значениями могут быть строки и null. Также вернет null, если
     * непрочитанных строк в результате больше не осталось.
     * @return string|null
     */
    public function readScalar()
    {
        $line = $this->readLine();
        if ($line) return current($line);
        else return null;
    }
}