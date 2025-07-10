<?php
$type = $_POST['type'] ?? '';
?>
<fieldset style="width:105px; position:absolute; top:105px; left:700px">
    <legend>Type</legend>
    <label>Order <input name="type" value="Order" type="radio" onclick='typeChosen = true;' <?php if ($type == "Order") echo "checked='yes'"; ?> /></label><br />
    <label>Inquiry/Quote <input name="type" value="Inquiry/Quote" type="radio" onclick='typeChosen = true;' <?php if ($type == "Inquiry/Quote") echo "checked='yes'"; ?> /></label><br />
    <label>Mock-up <input name="type" value="Mock-up" type="radio" onclick='typeChosen = true;' <?php if ($type == "Mock-up") echo "checked='yes'"; ?> /></label><br />
    <label>Ready to Print <input name="type" value="Ready to Print" type="radio" onclick='typeChosen = true;' <?php if ($type == "Ready to Print") echo "checked='yes'"; ?> /></label><br />
    <label>Project <input name="type" value="Project" type="radio" onclick='typeChosen = true;' <?php if ($type == "Project") echo "checked='yes'"; ?> /></label><br />
    <label>Inactive <input name="type" value="Inactive" type="radio" onclick='typeChosen = true;' <?php if ($type == "Inactive") echo "checked='yes'"; ?> /></label><br />
    <label>Other <input name="type" value="Other" type="radio" onclick='typeChosen = true;' <?php if ($type == "Other") echo "checked='yes'"; ?> /></label><br />
    <?php
    $str = 'MHM,HP-FB500,SST,Cameo,Outsource,Other';
    $ary = explode(',', $str);
    //print_r($ary);
    ?>
    <select name="type"><!--style="width:105px; position:absolute; top:270px; left:700px">-->
        <option value="">Production Method</option>
        <?php
        foreach ($ary as $drop) {
            echo "<option value=$drop>$drop</option>";
        }
        ?>
    </select>
</fieldset>