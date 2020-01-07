<?php namespace frame\tools;

class JsonEncoder
{
    public function toValidJson($value): string
    {
        return json_encode(
            $value,
            JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES
        );
    }

    public function forViewText($value): string
    {
        return $this->toValidJson($value);
    }

    public function forHtmlAttribute($value, bool $isSubJson = false): string
    {
        $json = $this->toValidJson($value);
        return $isSubJson ? $json : str_replace('"', '&quot;', $json);
    }

    public function forJavascriptString($value, bool $isSubJson = false): string
    {
        $json = $this->toValidJson($value);
        return $isSubJson ? $json : str_replace(
            ['\\', '"'],
            ['\\\\', '\"'],
            $json
        );
    }
}