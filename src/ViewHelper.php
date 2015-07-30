<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class ViewHelper
{
    /**
     * Render a table row.
     *
     * @param string $component   Component
     * @param string $old_version Old Version
     * @param string $new_version New Version
     */
    public static function renderTableRow($component, $old_version, $new_version, $update = false)
    {
        $link =  'index.php?action=scanComponent&component=' . $component;

        $html = '<tr>';
        $html .= '<td>' . $component . '</td>';
        $html .= '<td>' . $old_version . '</td>';

        $html .= ((bool)$update === true)
            ? '<td>' . self::printUpdatedSign($new_version, $component) . '</td>'
            : '<td>&nbsp;</td>';

        $html .= '<td>' . self::renderAnchorButton($link, 'Scan') . '</td>';
        $html .= '</tr>';

        return $html;
    }

    /**
     * Print an update symbol for the new_version.
     *
     * @param string $new_version New version.
     * @param string $component   Component
     */
    public static function printUpdatedSign($new_version, $component)
    {
        $link =  'index.php?action=update-component&component=' . $component;

        $html = '<span class="badge alert-success">' . $new_version . '</span>';
        $html .= '<span style="color:green; font-size: 16px">&nbsp;&#x25B2;&nbsp;</span>';
        $html .= self::renderAnchorButton($link, 'Commit & Push');

        return $html;
    }

    /**
     * Render an anchor tag.
     *
     * @param string $link An URL, the href.
     * @param string $text Link Text.
     */
    public static function renderAnchorButton($link, $text)
    {
        return '<a class="btn btn-default btn-xs" href="' . $link . '">' . $text . '</a>';
    }
}