<?php
/**
 * Endpoint Manager Object Module - Sec Devices
 *
 * @author Javier Pastor
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */

namespace FreePBX\modules;

#[\AllowDynamicProperties]
class Endpointman_Devices
{
	// public function __construct($freepbx = null, $cfgmod = null) 
	public function __construct($epm) 
	{
		$epm			 = $epm;
		$this->freepbx   = $epm->freepbx;
		$this->db 	     = $epm->freepbx->Database;
		$this->config    = $epm->freepbx->Config;
	}

	public function myShowPage(&$pagedata) {
		if(empty($pagedata))
		{
			$pagedata['main'] = array(
				"name" => _("Devices"),
				"page" => '/views/epm_devices_main.page.php'
			);
		}
	}

	public function showPage(array &$data)
	{
		// Get the Endpointman instance
		$epm = \FreePBX::Endpointman();

		// 1. Get all devices and products
		$devices_raw = $epm->eda->all_devices();
		$products = $epm->eda->all_products();

		// 2. Get device statuses from Asterisk
		$asterisk_location = $epm->epm->getConfig("asterisk_location");
		$device_statuses_output = shell_exec($asterisk_location . " -rx 'sip show peers'");
		$device_statuses_lines = explode("\n", $device_statuses_output);
		$devices_status = [];
		foreach ($device_statuses_lines as $line) {
			preg_match('/(\d*)\/[\d]*/i', $line, $extout);
			preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/i', $line, $ipaddress);
			if (!empty($extout[1])) {
				$devices_status[$extout[1]] = [
					'status' => (bool)preg_match('/OK \(.*\)/i', $line),
					'ip' => $ipaddress[0] ?? '',
				];
			}
		}

		// 3. Build device list for the template
		$devices = [];
		foreach ($devices_raw as $device) {
			$lines = $epm->eda->get_lines_from_device($device['id']);
			$first_line = $lines[0] ?? [];
			$ext = $first_line['ext'] ?? null;

			// Get template name
			if (($device['template_id'] == 0) && isset($device['global_custom_cfg_data'])) {
				$template_name = "Custom-" . $device['mac'];
			} elseif ((!isset($device['custom_cfg_data'])) && ($device['template_id'] == 0)) {
				$template_name = "N/A";
			} else {
				$sql = "SELECT name FROM endpointman_template_list WHERE id = " . $device['template_id'];
				$template_name = $epm->eda->sql($sql, 'getOne');
			}

			// Get brand name
			$brand_info = $epm->get_brand_from_mac($device['mac']);
			$brand_name = $brand_info['name'] ?? '';

			$devices[] = [
				'id'        => $device['id'],
				'mac'       => $device['mac'],
				'brand'     => $brand_name,
				'model'     => $device['model'] . (!$device['enabled'] ? " <i>("._("Disabled").")</i>" : ""),
				'line'      => $first_line['line'] ?? '',
				'extension' => $first_line['ext'] ?? '',
				'description' => $first_line['description'] ?? '',
				'template'  => $template_name,
				'status'    => $devices_status[$ext]['status'] ?? false,
				'ip'        => $devices_status[$ext]['ip'] ?? '',
			];
		}

		// 4. Get brands, models, templates for dropdowns
		$brands = $epm->brands_available();

		// Models
		$sql = "SELECT DISTINCT endpointman_model_list.* FROM endpointman_product_list, endpointman_model_list WHERE endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_model_list.hidden = 0 AND endpointman_model_list.enabled = 1 AND endpointman_product_list.hidden != 1 AND endpointman_product_list.cfg_dir !=  ''";
		$models_raw = $epm->eda->sql($sql, 'getAll', \PDO::FETCH_ASSOC);
		$models = [];
		foreach ($models_raw as $row) {
			$models[] = [
				'value' => $row['id'],
				'text'  => $row['model'],
			];
		}

		// Templates
		$sql = "SELECT DISTINCT endpointman_template_list.* FROM endpointman_template_list";
		$templates_raw = $epm->eda->sql($sql, 'getAll', \PDO::FETCH_ASSOC);
		$templates = [];
		foreach ($templates_raw as $row) {
			$templates[] = [
				'value' => $row['id'],
				'text'  => $row['name'],
			];
		}

		// 5. Assign to $data for the template
		$data['devices']   = $devices;
		$data['brands']    = $brands;
		$data['models']    = $models;
		$data['templates'] = $templates;

		// You can add more as needed for your UI (e.g., available extensions, product list, etc.)
	}

	public function ajaxRequest($req, &$setting) {
		/*
		$arrVal = array("");
		if (in_array($req, $arrVal)) {
			$setting['authenticate'] = true;
			$setting['allowremote'] = false;
			return true;
		}
		*/
		return false;
	}
	
    public function ajaxHandler($module_tab = "", $command = "") 
	{
		$retarr = "";
		if ($module_tab == "manager")
		{
			switch ($command)
			{
				default:
					$retarr = array("status" => false, "message" => _("Command not found!") . " [" .$command. "]");
					break;
			}
		}
		else {
			$retarr = array("status" => false, "message" => _("Tab not found!") . " [" .$module_tab. "]");
		}
		return $retarr;
	}
	
	public function doConfigPageInit($module_tab = "", $command = "") { }
	
	public function getRightNav($request, $params = array()) {
		return "";
	}
	
	public function getActionBar($request) {
		return "";
	}
	
}