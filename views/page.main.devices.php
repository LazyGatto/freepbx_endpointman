<?php if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); } ?>

<div class="container-fluid">
    <h2><?= _('Devices') ?></h2>
    <div class="fpbx-container">

        <!-- Info Alert -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info fa-4x fa-pull-left fa-border" aria-hidden="true"></i>
            <?= _('This page helps you manage your phone devices.') ?><br>
            <?= _('Add, edit, or remove devices and assign templates as needed.') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Add/Edit Device Form -->
        <form id="adding" method="POST" action="">
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th></th>
                        <th><?= _('MAC Address') ?></th>
                        <th><?= _('Brand') ?></th>
                        <th><?= _('Model of Phone') ?></th>
                        <th><?= _('Line') ?></th>
                        <th><?= _('Extension Number') ?></th>
                        <th><?= _('Template') ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            <input name="mac" type="text" tabindex="1" size="17" maxlength="17" class="form-control" value="<?= htmlspecialchars($edit_mac ?? '') ?>">
                        </td>
                        <td>
                            <select name="brand_list" id="brand_edit" class="form-control">
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['value'] ?>" <?= !empty($brand['selected']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['text']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="model_list" id="model_new" class="form-control">
                                <?php foreach ($models as $model): ?>
                                    <option value="<?= $model['value'] ?>" <?= !empty($model['selected']) ? 'selected' : '' ?>><?= htmlspecialchars($model['text']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="line_list" id="line_list" class="form-control">
                                <option></option>
                                <!-- Populate via JS if needed -->
                            </select>
                        </td>
                        <td>
                            <select name="ext_list" id="ext_list" class="form-control">
                                <?php foreach ($display_ext ?? [] as $ext): ?>
                                    <option value="<?= $ext['value'] ?>"><?= htmlspecialchars($ext['text']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <select name="template_list" id="template_list" class="form-control">
                                    <?php foreach ($templates as $template): ?>
                                        <option value="<?= $template['value'] ?>" <?= !empty($template['selected']) ? 'selected' : '' ?>><?= htmlspecialchars($template['text']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <a href="#" onclick="return popitup('config.php?display=epm_config&amp;quietmode=1&amp;handler=file&amp;file=popup.html.php&amp;module=endpointman&amp;pop_type=edit_template&amp;edit_id=<?= $edit_id ?? 0 ?>', 'Template Editor', '<?= $edit_id ?? 0 ?>')" class="btn btn-outline-secondary"><i class="fa fa-pencil"></i></a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($mode) && $mode === 'EDIT'): ?>
                                <button type="submit" name="button_save" class="btn btn-primary" onclick="edit_device('edit',<?= $edit_id ?>,'button_save');"><i class="fa fa-save"></i> <?= _('Save') ?></button>
                            <?php else: ?>
                                <button type="button" name="button_add" class="btn btn-success" onclick="add_device();"><i class="fa fa-plus"></i> <?= _('Add') ?></button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (empty($mode) || $mode !== 'EDIT'): ?>
                                <button type="reset" class="btn btn-secondary"><i class="fa fa-undo"></i> <?= _('Reset') ?></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <!-- Unmanaged Devices Table (if searched) -->
        <?php if (!empty($searched) && !empty($unmanaged)): ?>
            <h3><?= _('Unmanaged Extensions') ?></h3>
            <form id="unmanaged" method="POST" action="">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?= _('MAC Address') ?></th>
                            <th><?= _('Brand') ?></th>
                            <th><?= _('Model') ?></th>
                            <th></th>
                            <th><?= _('Extension') ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unmanaged as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="add[]" value="<?= $row['id'] ?>"></td>
                                <td><?= htmlspecialchars($row['mac_strip']) ?><br>(<?= htmlspecialchars($row['ip']) ?>)</td>
                                <td><?= htmlspecialchars($row['brand']) ?></td>
                                <td>
                                    <select name="model_list_<?= $row['id'] ?>" class="form-control">
                                        <?php foreach ($row['list'] as $model): ?>
                                            <option value="<?= $model['id'] ?>"><?= htmlspecialchars($model['model']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td></td>
                                <td>
                                    <select name="ext_list_<?= $row['id'] ?>" class="form-control">
                                        <?php foreach ($display_ext ?? [] as $ext): ?>
                                            <option value="<?= $ext['value'] ?>"><?= htmlspecialchars($ext['text']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="button_add_selected_phones" class="btn btn-success" onclick="add_searched_devices();"><?= _('Add Selected Phones') ?></button>
                <label><input type="checkbox" name="reboot_sel"> <?= _('Reboot Phones') ?></label>
            </form>
        <?php endif; ?>

        <!-- Managed Devices Table -->
        <form id="managed" method="POST" action="config.php?type=tool&amp;display=epm_devices">
            <h3><?= _('Current Managed Extensions') ?></h3>
            <div class="mb-2">
                <button type="button" id="selecter" class="btn btn-outline-info btn-sm" onclick="togglePhones(true)"><i class="fa fa-check"></i> <?= _('Select All') ?></button>
                <button type="button" id="deselecter" class="btn btn-outline-info btn-sm" onclick="togglePhones(false)"><i class="fa fa-square-o"></i> <?= _('Deselect All') ?></button>
                <button type="button" id="expander" class="btn btn-outline-info btn-sm" onclick="toggleDisplayAll('expand')"><i class="fa fa-chevron-down"></i> <?= _('Expand All') ?></button>
                <button type="button" id="collapser" class="btn btn-outline-info btn-sm" onclick="toggleDisplayAll('collapse')"><i class="fa fa-chevron-up"></i> <?= _('Collapse All') ?></button>
            </div>
            <table class="table table-striped" id="devList">
                <thead>
                    <tr>
                        <th></th>
                        <th><?= _('MAC Address') ?></th>
                        <th><?= _('Brand') ?></th>
                        <th><?= _('Model of Phone') ?></th>
                        <th><?= _('Line') ?></th>
                        <th><?= _('Extension Number') ?></th>
                        <th><?= _('Template') ?></th>
                        <th><?= _('Edit') ?></th>
                        <th><?= _('Delete') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td><i class="fa fa-power-off <?= $device['status'] ? 'text-success' : 'text-danger' ?>" title="<?= htmlspecialchars($device['ip']) ?>"></i>
                                <input type="checkbox" class="device" name="selected[]" value="<?= $device['id'] ?>">
                            </td>
                            <td><?= htmlspecialchars($device['mac']) ?></td>
                            <td><?= htmlspecialchars($device['brand']) ?></td>
                            <td><?= htmlspecialchars($device['model']) ?></td>
                            <td><?= htmlspecialchars($device['line']) ?></td>
                            <td><?= htmlspecialchars($device['extension']) ?></td>
                            <td><a href="#" onclick="submit_stype('edit',<?= $device['id'] ?>);"><?= htmlspecialchars($device['template']) ?></a></td>
                            <td><a href="#" onclick="submit_wtype('edit',<?= $device['id'] ?>);"><i class="fa fa-pencil text-primary"></i></a></td>
                            <td><a href="#" onclick="delete_device(<?= $device['id'] ?>);"><i class="fa fa-trash text-danger"></i></a></td>
                        </tr>
                        <!-- Optionally, add line details as expandable rows here -->
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Bulk Actions -->
            <h4><?= _('Selected Phone(s) Options') ?></h4>
            <p>
                <button type="submit" name="button_delete_selected_phones" class="btn btn-danger" onclick="managed_options('delete_selected_phones');"><i class="fa fa-trash"></i> <?= _('Delete') ?></button>
                <?= _('Delete Selected Phones') ?>
            </p>
            <p>
                <button type="submit" name="button_rebuild_selected" class="btn btn-success" onclick="managed_options('rebuild_selected_phones');"><i class="fa fa-refresh"></i> <?= _('Rebuild') ?></button>
                <?= _('Rebuild Configs for Selected Phones') ?>
                <label><input type="checkbox" name="reboot"> <?= _('Reboot Phones') ?></label>
            </p>
            <p>
                <button type="submit" name="button_update_phones" class="btn btn-primary" onclick="managed_options('change_brand');"><i class="fa fa-random"></i> <?= _('Update') ?></button>
                <?= _('Change Selected Phones to') ?>
                <select name="brand_list_selected" id="brand_list_selected" class="form-control d-inline-block w-auto">
                    <option><?= _('Brand') ?></option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['value'] ?>" <?= !empty($brand['selected']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['text']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="model_list_selected" id="model_list_selected" class="form-control d-inline-block w-auto">
                    <option><?= _('Model') ?></option>
                    <?php foreach ($models as $model): ?>
                        <option value="<?= $model['value'] ?>"><?= htmlspecialchars($model['text']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label><input type="checkbox" name="reboot_change"> <?= _('Reboot Phones') ?></label>
            </p>
        </form>

        <!-- Global Phone Options -->
        <h4><?= _('Global Phone Options') ?></h4>
        <form id="go" method="POST" action="config.php?type=tool&amp;display=epm_devices" class="form-inline mb-2">
            <button type="submit" name="button_go" id="button_go" class="btn btn-info" onclick="find_devices();"><i class="fa fa-search"></i> <?= _('Search') ?></button>
            <?= _('Search for new devices in netmask') ?>
            <input name="netmask" type="text" value="<?= htmlspecialchars($netmask ?? '') ?>" class="form-control mx-2">
            <label><input name="nmap" type="checkbox" value="1" checked> <?= _('Use NMAP') ?></label>
        </form>
        <form id="globalmanaged" method="POST" action="">
            <button type="submit" name="button_rebuild_configs_for_all_phones" class="btn btn-success" onclick="submit_global('rebuild_configs_for_all_phones');"><i class="fa fa-refresh"></i> <?= _('Rebuild') ?></button>
            <?= _('Rebuild Configs for All Phones') ?>
            <label><input type="checkbox" name="reboot"> <?= _('Reboot Phones') ?></label>
        </form>
        <form id="globalmanaged2" method="POST" action="">
            <button type="submit" name="button_reboot_this_brand" class="btn btn-danger" onclick="submit_global2('reboot_brand');"><i class="fa fa-power-off"></i> <?= _('Reboot') ?></button>
            <?= _('Reboot This Brand') ?>
            <select name="rb_brand" class="form-control d-inline-block w-auto">
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['value'] ?>"><?= htmlspecialchars($brand['text']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <form id="globalmanaged3" method="POST" action="">
            <button type="submit" name="button_rebuild_reboot" class="btn btn-primary" onclick="submit_global3('rebuild_reboot');"><i class="fa fa-random"></i> <?= _('Configure') ?></button>
            <?= _('Reconfigure all (products)') ?>
            <select name="product_select" id="product_select" class="form-control d-inline-block w-auto">
                <?php foreach ($product_list ?? [] as $product): ?>
                    <option value="<?= $product['value'] ?>"><?= htmlspecialchars($product['text']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= _('with') ?>
            <select name="template_selector" id="template_selector" class="form-control d-inline-block w-auto">
                <option></option>
                <!-- Populated by JS -->
            </select>
            <label><input type="checkbox" name="reboot"> <?= _('Reboot Phones') ?></label>
        </form>
        <form id="globalmanaged4" method="POST" action="">
            <button type="submit" name="button_rebuild_reboot" class="btn btn-primary" onclick="submit_global3('mrebuild_reboot');"><i class="fa fa-random"></i> <?= _('Configure') ?></button>
            <?= _('Reconfigure all (models)') ?>
            <select name="model_select" id="model_select" class="form-control d-inline-block w-auto">
                <?php foreach ($models as $model): ?>
                    <option value="<?= $model['value'] ?>"><?= htmlspecialchars($model['text']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= _('with') ?>
            <select name="model_template_selector" id="model_template_selector" class="form-control d-inline-block w-auto">
                <option></option>
                <!-- Populated by JS -->
            </select>
            <label><input type="checkbox" name="reboot"> <?= _('Reboot Phones') ?></label>
        </form>
    </div>
</div>

<script src="assets/js/epm_devices.js"></script>