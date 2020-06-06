<?php namespace frame\console\views;

use frame\console\views\ConsolePage;
use frame\views\DynamicPage;
use frame\views\ViewRouter;

class ConsoleViewRouter extends ViewRouter
{
    public function getPageClass(): string
    {
        return ConsolePage::class;
    }

    public function getDynamicPageClass(): string
    {
        return DynamicPage::class;
    }
}