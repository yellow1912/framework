<?php

use Zepluf\Bundle\StoreBundle\Component\Payment\Method\PaypalStandard;

use \Doctrine\ORM\EntityManager;
use \Doctrine\Common\Collections\ArrayCollection;

$data['sandbox_mode'] = true;
$data['sandbox_notify'] = 'Sandbox notify';

if ($data['sandbox_mode']) {
    $data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
} else {
    $data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
}

$data['business'] = 'seller.1314@yahoo.com';

$data['products'] = array();
for ($i = 1; $i <= 5; $i++) {
    $data['products'][] = array(
        'name'       => 'Product 0' . $i,
        'price'      => 0 * $i * 10.73,
        'quantity'   => $i,
        'features'   => array(
            array('fName_1', 'fValue_1 '),
            array('fName_2', 'fValue_2 ')
        )
    );
}

if (!empty($_POST)) {
    $request = print_r($_POST, true);

    file_put_contents('request.txt', $request);
    exit();
} else {
    $data = unserialize(file_get_contents('cart_info.txt'));
}
?>
<html>
<head>

</head>

<body>
<form action="<?php echo $data['action']; ?>" method="post">
    <input type="hidden" name="cmd" value="<?php echo $data['cmd']; ?>" />
    <input type="hidden" name="business" value="<?php echo $data['business']; ?>" />
    <input type="hidden" name="custom" value="<?php echo $data['custom']; ?>" />
    <input type="hidden" name="currency_code" value="<?php echo $data['currency_code']; ?>" />
    <input type="hidden" name="notify_url" value="<?php echo $data['notify_url']; ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo $data['cancel_return']; ?>" />

    <input type="hidden" name="upload" value="1" />
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
<!--     <input type="hidden" name="return" value="http://vinhtrinh.no-ip.org/framework/index.php" />
    <input type="hidden" name="notify_url" value="http://vinhtrinh.no-ip.org/framework/index.php" />
    <input type="hidden" name="cancel_return" value="http://vinhtrinh.no-ip.org/framework/index.php" /> -->
    <input type="hidden" name="paymentaction" value="authorization" />


    <?php foreach ($data['items'] as $index => $item): ?>
    <?php foreach ($item as $name => $value): ?>
    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
    <?php endforeach; ?>
    <?php endforeach; ?>

    <input type="submit" value="Confirm Order" class="button" />
</form>
</body>
</html>
