<?php
require_once('./lib/Stripe.php');

$stripe = array(
        "secret_key"      => "sk_test_c31Wu4QOoacfEzv0xyR7zAQg",
        "publishable_key" => "pk_test_IRktDTwSOnNGyJiWi4kjA5Xj"
    );

Stripe::setApiKey($stripe['secret_key']);
?>
