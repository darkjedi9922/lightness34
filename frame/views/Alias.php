<?php namespace frame\views;

class _AliasView extends View
{
    public static function getExtensions(): array
    {
        return ['php'];
    }
    public static function getNamespace(): string
    {
        return 'aliases';
    }
};

class Alias
{
    private $alias;
    private $contents;
    private $args;

    /**
     * @return static|null
     */
    public static function resolveAlias(string $alias)
    {
        $viewfile = _AliasView::find($alias);
        if ($viewfile) return static::createAlias($alias, $alias, []);

        $dynamicRouter = new ViewDynamicRouter(_AliasView::class);
        $route = $dynamicRouter->findRealRoute($alias);
        if ($route) return static::createAlias($alias, $route->url, $route->args);

        return null;
    }

    /**
     * @return static
     */
    private static function createAlias(string $alias, string $viewname, array $args)
    {
        $view = new _AliasView($viewname);
        $aliasObject = new static($alias, $view->getHtml(), $args);
        return $aliasObject;
    }

    public function __construct(string $alias, string $contents, array $dynamicArgs)
    {
        $this->alias = $alias;
        $this->contents = $contents;
        $this->args = $dynamicArgs;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getDynamicArgs(): array
    {
        return $this->args;
    }

    public function __toString(): string
    {
        return $this->getContents();
    }
}