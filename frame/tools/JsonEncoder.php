<?php namespace frame\tools;

class JsonEncoder
{
    public static function toValidJson($value, bool $pretty = false): string
    {
        return json_encode(
            $value,
            JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES
                | ($pretty ? JSON_PRETTY_PRINT : 0)
        );
    }

    public static function forViewText($value, bool $pretty = false): string
    {
        return static::toValidJson($value, $pretty);
    }

    public static function forHtmlAttribute(
        $value,
        bool $isSubJson = false
    ): string {
        $json = static::toValidJson($value);
        return $isSubJson ? $json : str_replace('"', '&quot;', $json);
    }

    public static function forJavascriptString(
        $value,
        bool $isSubJson = false
    ): string {
        $json = static::toValidJson($value);
        return $isSubJson ? $json : str_replace(
            ['\\', '"'],
            ['\\\\', '\"'],
            $json
        );
    }
}