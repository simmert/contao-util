<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Util
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Util',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Lib
	'Util\AbstractContentElement' => 'system/modules/util/lib/AbstractContentElement.php',
	'Util\AbstractModel'          => 'system/modules/util/lib/AbstractModel.php',
	'Util\FragmentTemplate'       => 'system/modules/util/lib/FragmentTemplate.php',
	'Util\CurrencyHelper'         => 'system/modules/util/lib/Helper/CurrencyHelper.php',
	'Util\DcaHelper'              => 'system/modules/util/lib/Helper/DcaHelper.php',
	'Util\FormHelper'             => 'system/modules/util/lib/Helper/FormHelper.php',
	'Util\FragmentHelper'         => 'system/modules/util/lib/Helper/FragmentHelper.php',
	'Util\GeneralHelper'          => 'system/modules/util/lib/Helper/GeneralHelper.php',

	// Widgets
	'Util\Legend'                 => 'system/modules/util/widgets/Legend.php',
	'Util\ParentValue'            => 'system/modules/util/widgets/ParentValue.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_widget_legend'      => 'system/modules/util/templates/backend',
	'be_widget_parentvalue' => 'system/modules/util/templates/backend',
));
