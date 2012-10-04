<?php
   /**
    * WPИ-XM Server Stack
    * Jens-André Koch © 2010 - onwards
    * http://wpn-xm.org/
    *
    *        _\|/_
    *        (o o)
    +-----oOO-{_}-OOo------------------------------------------------------------------+
    |                                                                                  |
    |    LICENSE                                                                       |
    |                                                                                  |
    |    WPИ-XM Serverstack is free software; you can redistribute it and/or modify    |
    |    it under the terms of the GNU General Public License as published by          |
    |    the Free Software Foundation; either version 2 of the License, or             |
    |    (at your option) any later version.                                           |
    |                                                                                  |
    |    WPИ-XM Serverstack is distributed in the hope that it will be useful,         |
    |    but WITHOUT ANY WARRANTY; without even the implied warranty of                |
    |    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
    |    GNU General Public License for more details.                                  |
    |                                                                                  |
    |    You should have received a copy of the GNU General Public License             |
    |    along with this program; if not, write to the Free Software                   |
    |    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA    |
    |                                                                                  |
    +----------------------------------------------------------------------------------+
    */

#
# Update the "Components List" on the website http://wpn-xm.org/
#

/**
 * Reorder an array by keys.
 *
 * Reorder the keys of an array in order of specified keynames.
 * All other nodes not in $keynames will come after last $keyname, in normal array order.
 *
 * @param array &$array - the array to reorder
 * @param mixed $keynames - a csv or array of keynames, in the order that keys should be reordered
 */
function array_reorder_keys(&$array, $keynames) {

    if(empty($array) or !is_array($array) or empty($keynames)) {
        return;
    }

    if(!is_array($keynames)) {
        $keynames = explode(',',$keynames);
    }

    if(!empty($keynames)) {
        $keynames = array_reverse($keynames);
    }

    foreach($keynames as $n) {
        if(array_key_exists($n, $array)) {
            // copy the node before unsetting
            $newarray = array($n => $array[$n]);
            // remove the node
            unset($array[$n]);
            // combine copy with filtered array
            $array = $newarray + array_filter($array);
        }
    }
}

/**
 * Render the <ul id="components-list"> element.
 */
function render_list(array $html_list_array)
{
    // open > ul header and first list entry
    $html = '<ul id="components-list" class="unstyled-list big-and-bold">';
    $html .= '<li style="color: #999999;"><span id="windows-icon"> Windows </span><small>w32</small></li>';

    // render all list items
    foreach($html_list_array as $name => $item) {
        $html .= $item;
    }

    $html .= '</ul>';

    return $html;
}

/**
 * Update the website html with the new <ul id="components-list"> element.
 */
function replace_components_list($file, $content)
{
    // load file content as DOMDocument
    $dom = new DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile($file);
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    // fetch the <ul id="components-list" element
    $elem = $dom->getElementById("components-list");

    // add list's html content
    $newElement = $dom->createDocumentFragment();
    $newElement->appendXML($content);

    // replace
    $elem->parentNode->replaceChild($newElement, $elem);

    $html = $newElement->ownerDocument->saveHTML($newElement);

    $dom->saveHTMLFile($file);
}

/**
 * Creates an array
 * key = software component name
 * value = rendered list element containing link, name and version.
 */
function getHTMLArray(array $registry)
{
    $html_list_array = array();

    foreach($registry as $software) {

        $name = $website = $version = $version_html = '';

        $name = isset($software['name']) ? $software['name'] : '';
        $website = isset($software['website']) ? $software['website'] : '';
        $version = $software['latest']['version'];

        // skip rendering boring 1.0 version numbers
        if($version !== '1.0') {

            $version_html = ' <small>'.$version.'</small>';
        }

        // render only if we got a software name (also skip Clansuite)
        if($name !== '' and $name !== 'Clansuite') {
            $html_list_array[$name] =  '<li><a href="' . $website . '">' . $name . $version_html.'</a></li>';
        }
    }

    return $html_list_array;
}

/**
 * - Main -
 */

// load software components registry
$registry = include __DIR__ . '/wpnxm-software-registry.php';

// produce html list elements for each component in the registry
$html_list_array = getHTMLArray($registry);

// these keys will come first in the array
$sort_order_keys = array('PHP', 'Nginx', 'XDebug', 'MariaDB', 'Adminer', 'phpMyAdmin', 'Composer', 'PEAR', 'APC');

array_reorder_keys($html_list_array, $sort_order_keys);

// render the list elements into a ul-element
$newInnerHTML = render_list($html_list_array);

// replace old components-list element with the new content
replace_components_list('index.html', $newInnerHTML);
