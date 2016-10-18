<?php
/**
 * Settings Controller
 *
 * @package Vallejo Menu Plugin
 */

/**
 * Model
 */
require_once VMENU_PATH . 'model/model-vmenu-settings.php';


// Do logic.
$vmenu = vmenu();

$vmenu->settings = new Vallejo_Menu_Settings();


/**
 * View
 *
 * @see Vallejo_Menu_Settings::plugin_settings()
 */

