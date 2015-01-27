<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package		Jea
 * @copyright	Copyright (C) 2015 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Install Script file of JEA component
 */
class plgjeaalurInstallerScript
{
    /**
     * method to install the extension
     *
     * @return void
     */
    function install($parent)
    {

    }

    /**
     * method to uninstall the extension
     *
     * @return void
     */
    function uninstall($parent)
    {

    }

    /**
     * method to update the extension
     *
     * @return void
     */
    function update($parent)
    {

    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $db->setQuery('SHOW COLUMNS FROM #__jea_properties');
        $cols = $db->loadObjectList('Field');

        if (!isset($cols['alur'])) {
            $query = 'ALTER TABLE `#__jea_properties` '
            . "ADD `alur` TEXT NOT NULL DEFAULT ''";
            $db->setQuery($query);
            $db->query();
        }
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {

    }
}


