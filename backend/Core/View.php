<?php

namespace Core;

defined("APPPATH") or die("Access denied");

class View
{
    /**
     * @var
     */
    protected static $data;

    /**
     * @var
     */
    const VIEWS_PATH = "../App/views/";

    /**
     * @var
     */
    const EXTENSION_TEMPLATES = "php";

    /**
     * [render views with data]
     * @param  [String]  [template name]
     * @return [html]    [render html]
     */
    public static function render($template)
    {
        if (!file_exists(self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES)) {
            throw new \Exception("Error: El archivo " . self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES . " no existe", 1);
        }

        ob_start();
        extract(self::$data);
        include(self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES);
        $str = ob_get_contents();
        ob_end_clean();
        echo $str;
    }

    /**
     * [set Set Data form views]
     * @param [string] $name  [key]
     * @param [mixed] $value [value]
     */
    public static function set($name, $value)
    {
        self::$data[$name] = $value;
    }

    public static function fetch($template)
    {
        if (!file_exists(self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES)) {
            throw new \Exception("Error: El archivo " . self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES . " no existe", 1);
        }

        ob_start();
        extract(self::$data);
        include(self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES);
        // $str = ob_get_contents();
        $str = ob_get_clean();
        return $str;
    }

    public static function getPath($template)
    {
        return self::VIEWS_PATH . $template . "." . self::EXTENSION_TEMPLATES;
    }
}
