<?php

namespace WPNXM\Updater;

class View
{

    /**
     * Set data from controller: $view->data['variable'] = 'value';
     * @var array 
     */
    public $data = [];

    function render($template = '')
    {
        if (empty($template)) {
            $template = __DIR__ . '/view/' . self::getInstantiatingClass() . '.php';
        }

        if (!is_file($template)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        // define a closure with a scope for the variable extraction
        $result = function($file, array $data = array()) {
            ob_start();
            extract($data, EXTR_SKIP);
            try {
                include $file;
            } catch (\Exception $e) {
                ob_end_clean();
                throw $e;
            }
            return ob_get_clean();
        };

        // call the closure
        echo $result($template, $this->data);
    }

    protected static function getInstantiatingClass()
    {
        // find out where $view->render() was called
        $class = debug_backtrace(2, 3)[2]['class'];
        
        // remove the namespace
        return (substr($class, strrpos($class, '\\') + 1));
    }

}
