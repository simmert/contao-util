<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
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
	// Library
	'Util\AbstractContentElement' => 'system/modules/util/library/AbstractContentElement.php',
	'Util\AbstractFilter'         => 'system/modules/util/library/Filter/AbstractFilter.php',
	'Util\DateFilter'             => 'system/modules/util/library/Filter/DateFilter.php',
	'Util\MemberFilter'           => 'system/modules/util/library/Filter/MemberFilter.php',
	'Util\FragmentTemplate'       => 'system/modules/util/library/FragmentTemplate.php',
	'Util\ArrayHelper'            => 'system/modules/util/library/Helper/ArrayHelper.php',
	'Util\CurrencyHelper'         => 'system/modules/util/library/Helper/CurrencyHelper.php',
	'Util\DatabaseHelper'         => 'system/modules/util/library/Helper/DatabaseHelper.php',
	'Util\DcaHelper'              => 'system/modules/util/library/Helper/DcaHelper.php',
	'Util\FormHelper'             => 'system/modules/util/library/Helper/FormHelper.php',
	'Util\FragmentHelper'         => 'system/modules/util/library/Helper/FragmentHelper.php',
	'Util\GeneralHelper'          => 'system/modules/util/library/Helper/GeneralHelper.php',
	'Util\AbstractMemberModel'    => 'system/modules/util/library/Model/AbstractMemberModel.php',
	'Util\AbstractModel'          => 'system/modules/util/library/Model/AbstractModel.php',
	'Util\MemberModel'            => 'system/modules/util/library/Model/MemberModel.php',

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
