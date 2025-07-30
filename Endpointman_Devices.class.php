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
    $epm = \FreePBX::Endpointman();

    // 1. Get all products, devices, and available extensions
    $family_list = $epm->eda->all_products();
    $full_device_list = $epm->eda->all_devices();
    $ava_exts = $epm->display_registration_list();

    // 2. Get device statuses from Asterisk
    $asterisk_location = $epm->getConfig("asterisk_location");
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
    $i = 0;
    foreach ($full_device_list as $device) {
        $line_list = $epm->eda->get_lines_from_device($device['id']);
        $z = 0;
        // Template name logic
        if (($device['template_id'] == 0) && (isset($device['global_custom_cfg_data']))) {
            $template_name = "Custom-" . $device['mac'];
        } elseif ((!isset($device['custom_cfg_data'])) && ($device['template_id'] == 0)) {
            $template_name = "N/A";
        } else {
            $sql = "SELECT name FROM endpointman_template_list WHERE id = " . $device['template_id'];
            $template_name = $epm->eda->sql($sql, 'getOne');
        }
        // Brand name
        $brand_info = $epm->get_brand_from_mac($device['mac']);
        $brand_name = $brand_info['name'] ?? '';
        // Model (with disabled marker)
        $model = $device['model'];
        if (empty($device['enabled'])) {
            $model .= " <i>("._("Disabled").")</i>";
        }
        // Lines
        $lines = [];
        foreach ($line_list as $line_row) {
            $lines[] = [
                'ext' => $line_row['ext'],
                'line' => $line_row['line'],
                'description' => $line_row['description'],
                'luid' => $line_row['luid'],
                'ipei' => $line_row['ipei'],
            ];
        }
        $ext = $lines[0]['ext'] ?? null;
        $devices[] = [
            'id'        => $device['id'],
            'mac'       => $device['mac'],
            'brand'     => $brand_name,
            'model'     => $model,
            'line'      => $lines[0]['line'] ?? '',
            'extension' => $lines[0]['ext'] ?? '',
            'description' => $lines[0]['description'] ?? '',
            'template'  => $template_name,
            'status'    => $devices_status[$ext]['status'] ?? false,
            'ip'        => $devices_status[$ext]['ip'] ?? '',
            'lines'     => $lines,
        ];
        $i++;
    }

    // 4. Unmanaged (unknown) devices
    $unknown_list = $epm->eda->all_unknown_devices();
    $unmanaged = [];
    foreach ($unknown_list as $row) {
        $brand_info = $epm->get_brand_from_mac($row['mac']);
        $unmanaged[] = [
            'id' => $row['id'],
            'mac_strip' => $row['mac'],
            'ip' => $row['ip'] ?? '',
            'brand' => $brand_info['name'] ?? '',
            'brand_id' => $row['brand_id'] ?? '',
            'model' => _("Unknown"),
            'list' => $epm->eda->sql("SELECT * FROM endpointman_model_list WHERE enabled = 1 AND brand =".$row['brand_id'], 'getAll', \PDO::FETCH_ASSOC),
        ];
    }

    // 5. Product list for dropdowns
    $sql = "SELECT DISTINCT endpointman_product_list.* FROM endpointman_product_list, endpointman_model_list WHERE endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_model_list.hidden = 0 AND endpointman_model_list.enabled = 1 AND endpointman_product_list.hidden != 1 AND endpointman_product_list.cfg_dir !=  ''";
    $product_list_raw = $epm->eda->sql($sql, 'getAll', \PDO::FETCH_ASSOC);
    $product_list = [];
    $product_list[] = ['value' => 0, 'text' => ""];
    foreach ($product_list_raw as $row) {
        $product_list[] = ['value' => $row['id'], 'text' => $row['short_name']];
    }

    // 6. Model list for dropdowns
    $sql = "SELECT DISTINCT endpointman_model_list.* FROM endpointman_product_list, endpointman_model_list WHERE endpointman_product_list.id = endpointman_model_list.product_id AND endpointman_model_list.hidden = 0 AND endpointman_model_list.enabled = 1 AND endpointman_product_list.hidden != 1 AND endpointman_product_list.cfg_dir !=  ''";
    $model_list_raw = $epm->eda->sql($sql, 'getAll', \PDO::FETCH_ASSOC);
    $model_list = [];
    $model_list[] = ['value' => 0, 'text' => ""];
    foreach ($model_list_raw as $row) {
        $model_list[] = ['value' => $row['id'], 'text' => $row['model']];
    }

    // 7. Template list for dropdowns
    $sql = "SELECT DISTINCT endpointman_template_list.* FROM endpointman_template_list";
    $templates_raw = $epm->eda->sql($sql, 'getAll', \PDO::FETCH_ASSOC);
    $templates = [];
    foreach ($templates_raw as $row) {
        $templates[] = ['value' => $row['id'], 'text' => $row['name']];
    }

    // 8. Brands for dropdowns
    $brands = $epm->brands_available();

    // 9. Netmask for device search
    $netmask = !empty($epm->getConfig['nmap_search']) ? $epm->getConfig['nmap_search'] : ($_SERVER["SERVER_ADDR"] ?? '0.0.0.0').'/24';

    // 10. Assign all to $data for the template
    $data['devices']      = $devices;
    $data['unmanaged']    = $unmanaged;
    $data['brands']       = $brands;
    $data['models']       = $model_list;
    $data['templates']    = $templates;
    $data['product_list'] = $product_list;
    $data['display_ext']  = $ava_exts;
    $data['netmask']      = $netmask;
    $data['searched']     = !empty($unmanaged); // true if search found unmanaged devices

    // Optionally, add more UI state as needed (edit row, mode, etc.)
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