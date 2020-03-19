<?php namespace frame\lists\paged;

use frame\cash\router;
use frame\errors\HttpError;
use frame\lists\paged\PagerView;

/**
* Класс для реализации счетчика/переключателя страниц 
* по номерам для списков материалов.
*/
class PagerModel
{
    private $current;
    private $allCount;
    private $pageLimit;

    public static function calcLast(int $allCount, int $pageLimit): int {
        $last = ceil($allCount / $pageLimit);
        if ($last == 0) return 1;
        else return $last;
    }

    /**
     * @param int $current Номер текущей страницы.
     * @param int $allCount Количество материалов всего на всех страницах.
     * @param int $pageLimit Максимально допустимое количество материалов на 
     * одной странице.
     * 
     * @throws \Exception если pageLimit = 0 или, если current < 1 или > last
     */
    public function __construct(int $current, int $allCount, int $pageLimit) {
        if ($pageLimit == 0) throw new \Exception('Materials page amount is 0.');
        
        $this->current = $current;
        $this->allCount = $allCount;
        $this->pageLimit = $pageLimit;
        $this->last = self::calcLast($allCount, $pageLimit);
        
        if ($current < 1) throw new HttpError(HttpError::NOT_FOUND, 
            'The current page number is less than the minimum.');
        else if ($current > $this->last) throw new HttpError(HttpError::NOT_FOUND, 
            'The current page mumber is greater than the maximum.');
    }
    
    /**
     * Показывает вид, представляющий pager.
     * @see frame\lists\paged\PagerView::__construct()
     */
    public function show(string $name, string $layout = null) {
        (new PagerView($this, $name, $layout))->show();
    }
    
    public function getPevious(): ?int {
        if ($this->current - 1 > 0) return $this->current - 1;
        else return false;
    }
    
    public function getCurrent(): int {
        return $this->current;
    }
    
    public function getNext(): ?int {
        if ($this->current + 1 <= $this->last) return $this->current + 1;
        else return false;
    }
    
    public function getLast(): int {
        return static::calcLast($this->allCount, $this->pageLimit);
    }

    /**
     * Возвращает ссылку на ту же страницу, но с другим номером страницы.
     */
    public function toLink(string $urlPageArgumentName, int $pageNumber): string {
        return router::get()->toUrl([$urlPageArgumentName => $pageNumber]);
    }
    
    public function countPages(): int {
        return $this->getLast();
    }
    
    public function countPageLimit(): int {
        return $this->pageLimit;
    }

    public function countAllMaterials(): int {
        return $this->allCount;
    }
    
    /**
     * Индекс строки в БД, с которой нужно начинать выборку для данного 
     * номера страницы.
     */
    public function getStartMaterialIndex(): int {
        return ($this->current - 1) * $this->pageLimit;
    }
}