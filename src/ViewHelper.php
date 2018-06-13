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
        return '<a class="btn btn-secondary-outline btn-sm" href="' . $link . '">' . $text . '</a>';
    }
}