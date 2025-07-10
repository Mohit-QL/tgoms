<form>
    <div style="float:right; clear:right">
        <input class="topNaviButton" type='button' name='logout' value='Log Out' onclick='location.href="<?php echo BASE_URL; ?>index2.php?logout=1";' />
    </div>
    <img src="oms.gif" width='185px' height='84px' />
    <div style="position:absolute; left:200px; top:30px;">
        <input class="topNaviButton" type='button' name='neworder' value='New Order Form' onclick='location.href="<?php echo BASE_URL; ?>viewOrder.php?edit=2";' />
        <input class="topNaviButton" type='button' name='sortorders' value='Sort' onclick='location.href="<?php echo BASE_URL; ?>sort.php";' />
        <input class="topNaviButton" type='button' name='searchorders' value='Search' onclick='location.href="<?php echo BASE_URL; ?>search.php";' />
        <br />
        <input class="topNaviButton" type='button' name='department' value='Department' onclick='location.href="<?php echo BASE_URL; ?>department.php";' />
        <input class="topNaviButton" type='button' name='controlpanel' value='Control Panel' onclick='location.href="<?php echo BASE_URL; ?>control.php";' />
        <input class="topNaviButton" type='button' name='ordergoods' value='Order Goods' onclick='location.href="<?php echo BASE_URL; ?>orderGoods.php";' />
    </div>
</form>