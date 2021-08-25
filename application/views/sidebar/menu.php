<ul class="sidebar-menu" data-widget="tree">
    <li id='li-dashboard'>
        <a href="<?php echo base_url();  ?>index.php">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
    </li>
    <li id='li-weekdata'>
        <a href="<?php echo base_url();  ?>index.php/weekview">
            <i class="fa fa-calendar"></i> <span>Weekly Data</span>
        </a>
    </li>
    <li id='li-productmovement'>
        <a href="<?php echo base_url(); ?>index.php/productmovement">
            <i class="fa fa-bar-chart"></i> <span>Product Movement</span>
        </a>
    </li>
    <li id='li-report'>
        <a href="<?php echo base_url(); ?>index.php/report">
            <i class="fa fa-file-text-o"></i> <span>Reports</span>
        </a>
    </li>
    <li class="header"></li>
    <li id='li-product'><a href="<?php echo base_url(); ?>index.php/product"><i class="fa fa-cubes"></i> <span>Product</span></a></li>
    <li id='li-uom'><a href="<?php echo base_url(); ?>index.php/uom"><i class="fa fa-sliders"></i> <span>Unit of Measurement</span></a></li>
    <li id='li-branch'><a href="<?php echo base_url(); ?>index.php/branch"><i class="fa fa-home"></i> <span>Branch</span></a></li>
    <?php
    // $rawmatmasterlist = '';
    // if($_SESSION["rgc_access_level"] == 0){
    $rawmatmasterlist = '<li id="li-rawmat_master"><a href="' . base_url() . 'index.php/rawmaterial"><i class="fa fa-circle-o"></i> Master List</a></li>';
    // }

    $rawmatdata =
        '<li class="treeview" id="li-rawmat_menu" style="height: auto;">
        <a href="#">
        <i class="fa fa-asterisk"></i> <span>Raw Materials</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
        </a>
        <ul class="treeview-menu">
            <li id="li-rawmat_dailyinv"><a href="' . base_url() . 'index.php/dailyinventory"><i class="fa fa-circle-o"></i> Daily Inventory </a></li>
            <li id="li-rawmat_comm"><a href="#"><i class="fa fa-circle-o"></i> Comissary Delivery</a></li>
            <li id="li-rawmat_local"><a href="#"><i class="fa fa-circle-o"></i> Local Delivery </a></li>
            ' . $rawmatmasterlist . '
        </ul>
    </li>';

    echo $rawmatdata;
    if ($_SESSION["rgc_access_level"] == 0) {
        echo '<li id="li-conversion"><a href="' . base_url() . 'index.php/conversion"><i class="fa fa-balance-scale"></i> <span>Conversion</span></a></li>';
        echo '<li id="li-userlist"><a href="' . base_url() . 'index.php/userlist"><i class="fa fa-users"></i> <span>User List</span></a></li>';
    }
    ?>
    <li class="header"></li>
    <li><a href="#" id="changepass_btn"><i class="fa fa-key"></i> <span>Change Password</span></a></li>
</ul>