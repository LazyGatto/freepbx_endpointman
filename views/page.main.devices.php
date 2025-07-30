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

        <!-- Device Table -->
        <table 
            id="hwgrid"
            data-cache="false"
            data-toggle="table"
            data-pagination="true"
            data-show-columns="true"
            data-show-toggle="true"
            data-search="true"
            data-toolbar="#toolbar-all-hwgrid"
            class="table table-striped">
            <thead>
                <tr>
                    <th><?= _('Status') ?></th>
                    <th><?= _('MAC Address') ?></th>
                    <th><?= _('Brand') ?></th>
                    <th><?= _('Model') ?></th>
                    <th><?= _('Line') ?></th>
                    <th><?= _('Extension') ?></th>
                    <th><?= _('Template') ?></th>
                    <th><?= _('Edit') ?></th>
                    <th><?= _('Delete') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $device): ?>
                <tr>
                    <td>
                        <i class="icon-off icon-large <?= $device['status'] ? 'green' : 'red' ?>" title="<?= $device['ip'] ?>"></i>
                    </td>
                    <td><?= htmlspecialchars($device['mac']) ?></td>
                    <td><?= htmlspecialchars($device['brand']) ?></td>
                    <td><?= htmlspecialchars($device['model']) ?></td>
                    <td><?= htmlspecialchars($device['line']) ?></td>
                    <td><?= htmlspecialchars($device['extension']) ?></td>
                    <td><?= htmlspecialchars($device['template']) ?></td>
                    <td>
                        <a href="#" onclick="editDevice(<?= $device['id'] ?>);"><i class="fa fa-pencil"></i></a>
                    </td>
                    <td>
                        <a href="#" onclick="deleteDevice(<?= $device['id'] ?>);"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bulk/Global Actions (add as needed) -->
        <!-- ... -->
    </div>
</div>