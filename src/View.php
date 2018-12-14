<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater;

class View
{

    /**
     * Set data from controller: $view->data['variable'] = 'value';
     * @var array
     */
    public $data = [];

    public function renderTemplate($template = '')
    {
        // fetch template by name
        if (empty($template)) {
            $template = __DIR__ . '/view/' . self::getInstantiatingClass() . '.php';
        }

        if (!is_file($template)) {
            throw new \RuntimeException('Template not found: ' . $template);
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

    public function render()
    {
        $this->renderTemplate(TPL_DIR . 'header.html');       
        $this->renderTemplate(); // -> renders main action
        $this->renderTemplate(TPL_DIR . 'footer.html');
    }

    protected static function getInstantiatingClass()
    {
        // find out where $view->render() was called
        $class = debug_backtrace(2, 4)[3]['class'];

        // remove the namespace
        return (substr($class, strrpos($class, '\\') + 1));
    }

}
