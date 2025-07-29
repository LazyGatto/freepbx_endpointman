<?php if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); } ?>

<?= $endpoint_warn ?>
<div class="container-fluid">
	<h2><?= _('Device')?></h2>
	<div class="fpbx-container">
		<div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info fa-4x fa-pull-left fa-border" aria-hidden="true"></i>
            <?= _('This page helps you building your phone Packages.')?><br>
            <br>
            <?= _('Create or modify your config files and replace the needed values with the Placeholders.')?><br />
            <?= _('With this information you can add new Phones to OSS EPM within minutes.')?><br />
            <br />
            <?= _('If you need a specific value to add your Phone you can make a feature request.')?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

		<div id="toolbar-all-hwgrid">
        </div>
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
					<th width="7%""></th>
					<th width="13%" align='center'><?= _('MAC Address')?></th>
					<th width="13%" align='center'><?= _('Brand')?></th>
					<th width="10%" align='center'><?= _('Model of Phone')?></th>
					<th width="10%" align='center'><?= _('Line')?></th>
					<th width="19%" align='center'><?= _('Extension Number')?></th>
					<th width="15%" align='center'><?= _('Template')?></th>
					<th width="6%"></th>
					<th width="7%"></th>
				</tr>
			</thead>
			<tbody>
            </tbody>
        </table>

	</div>
</div>