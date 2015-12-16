<?php

/**
* ReorderElementTexts plugin.
*
* @package Omeka\Plugins\ConditionalElements
*/
class ReorderElementTextsPlugin extends Omeka_Plugin_AbstractPlugin {
	/**
	* @var array This plugin's hooks.
	*/
	protected $_hooks = array(
		'initialize',
		'install',
		'uninstall',
		'admin_head',
	);

  protected $_options = array(
    // 'conditional_elements_dependencies' => "[]",
  );

  /**
  * Install the plugin.
  */
  public function hookInstall() {
    SELF::_installOptions();
  }

  /**
  * Uninstall the plugin.
  */
  public function hookUninstall() {
    SELF::_uninstallOptions();
  }

   /**
     * Add the translations.
     */
  public function hookInitialize() {
    add_translation_source(dirname(__FILE__) . '/languages');
  }

	public function hookAdminHead($args) {
		// Core hookAdminHead taken from ElementTypes plugin

		$request = Zend_Controller_Front::getInstance()->getRequest();

		$module = $request->getModuleName();
		if (is_null($module)) { $module = 'default'; }

		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if ($module === 'default' &&
				$controller === 'items' &&
				in_array($action, array('add',  'edit')) ) {

			queue_js_string("
				var reorderElementTestsButton = '".__("Reorder Inputs")."';
				var reorderElementTextsUrl = '".html_escape(url('reorder-element-texts/index/reorder'))."';
			");
			queue_js_file('reorderelementtexts');
		} # if ($module === 'default' ...
	} # public function hookAdminHead()

} # class
