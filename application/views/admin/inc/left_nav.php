<style>
	.left_nav_icon_img {
		width: 15px;
	}

	.nav-icon {
		font-size: 10px !important
	}
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="<?= MAINSITE_Admin . 'wam' ?>" class="brand-link">
		<img src="<?= _lte_files_ ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
			class="brand-image img-circle elevation-3" style="opacity: .8">
		<span class="brand-text font-weight-light"><?= _brand_name_ ?></span>
	</a>
	<div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<?php echo form_open("", array('method' => 'get', 'id' => 'sidebar-form', "name" => "sidebar-form", 'style' => '', 'class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return false', 'autocomplete' => 'on')); ?>
		<div class="input-group">
			<input type="text" name="q" class="form-control" autocomplete="off" placeholder="Search..." id="search-input">

		</div>
		<?php echo form_close() ?>
		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column sidebar-menu tree" data-widget="treeview" role="menu"
				data-accordion="false">

				<!--Masters-->
				<?
				if (!empty($left_menu_master)) {
					$is_open = "";
					$active = "";
					if (!empty($page_is_master)) {
						if ($page_is_master == 1) {
							$is_open = "menu-open";
							$active = "active";
						}
					}
					?>
					<li class="nav-item has-treeview <?= $is_open ?>">
						<a href="#" class="nav-link <?= $active ?>"><i class="nav-icon fas fa-th"></i>
							<p>Masters<i class="fas fa-angle-left right"></i></p>
						</a>
						<ul class="nav nav-treeview"><?= $left_menu_master ?></ul>
					</li>
					<?
				}
				?>

			</ul>
		</nav>
		<!-- /.sidebar-menu -->
	</div>


</aside>