<?php

if (!empty($_POST)) {
    file_put_contents('request.txt', serialize($_POST));
    exit();
} else {
    $data = unserialize(file_get_contents('src/Zepluf/Bundle/StoreBundle/Tests/Component/Payment/Method/DataTest/cart_info.txt'));
}

?>
<html>
<head>

</head>

<body>
<form action="<?php echo $data['action']; ?>" method="post">
    <input type="hidden" name="upload" value="1" />

<?php foreach($data as $name => $value): if (!is_array($value) && $name !== 'action'): ?>
    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
<?php endif; endforeach; ?>

<?php if (isset($data['items'])) foreach ($data['items'] as $index => $item): foreach ($item as $name => $value): ?>
    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
<?php endforeach; endforeach; ?>

<!--
    <input type="hidden" name="first_name" value="Trinh" />
    <input type="hidden" name="last_name" value="Quang Vinh" />
    <input type="hidden" name="address1" value="Ho chi minh" />
    <input type="hidden" name="address2" value="" />
    <input type="hidden" name="city" value="Ho chi minh" />
    <input type="hidden" name="zip" value="00084" />
    <input type="hidden" name="country" value="VN" />
    <input type="hidden" name="address_override" value="0" />
    <input type="hidden" name="email" value="chjpz.doit@gmail.com" />

    <input type="hidden" name="invoice" value="19 - Trinh Quang Vinh" />
    <input type="hidden" name="lc" value="en" />
    <input type="hidden" name="rm" value="2" />
    <input type="hidden" name="no_note" value="1" />
    <input type="hidden" name="charset" value="utf-8" />
-->

    <input type="submit" value="Confirm Order" />
</form>
</body>
</html>