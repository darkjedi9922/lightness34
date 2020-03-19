<?php namespace frame\stdlib\drivers\route;

class UrlResponse extends \frame\route\Response
{
    private $redirect = null;

    /**
     * После выполнения скрипт завершается.
     */
    public function setUrl(string $url)
    {
        $this->redirect = $url;
        header('Location: ' . $url);
        $this->finish();
    }

    public function getUrl(): ?string
    {
        return $this->redirect;
    }

    /**
     * После выполнения скрипт завершается.
     */
    public function setText(string $text)
    {
        ob_end_clean_all();
        echo $text;
        $this->finish();
    }

    public function setCode(int $code)
    {
        http_response_code($code);
    }

    public function getCode(): int
    {
        return http_response_code();
    }
}