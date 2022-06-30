<?php

$values = array();
$values['fio'] =0;
$values['email'] =0;
$values['bd'] = 0;
$values['bio'] = 0;
$values['sex'] = 0;
$values['limbs'] = 0;

if (!preg_match("#^[aA-zZ0-9]+$#",$row['fio'])) {
$values['fio'] = 0;
}
else {
$values['fio'] = strip_tags($row['fio']);
}

if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
$values['email'] = 0;
}
else {
$values['email'] = strip_tags($row['email']);
}

if ($row['bd'] < 1900 & $row['bd'] > 2022) {
$values['bd'] = 0;
}
else {
$values['bd'] = strip_tags($row['bd']);
}

if (!preg_match('/^[MF]$/', $row['sex'])) {
$values['sex'] = 0;
}
else {
$values['sex'] = strip_tags($row['sex']);
}

if (!($row['limbs'] == 2 | $row['limbs'] == 4 | $row['limbs'] == 6 | $row['limbs'] == 8 | $row['limbs'] == 10 )) {
$values['limbs'] = 0;
}
else {
$values['limbs'] = strip_tags($row['limbs']);
}

$values['bio'] = strip_tags($row['bio']);

if (empty($_POST['abilities'])) {
    $values['abilities'] = 0;
}
else {
    foreach ($row['abilities'] as $super) {

        if (!($row['abilities'][$super] == 0 | $row['abilities'][$super] == 1 | $row['abilities'][$super] == 2 | $row['abilities'][$super] == 3)) {
            $_POST['abilities'][$super]=0;
        }
        else {
            setcookie('abilities_value_' . $super, '1', time() + 30 * 24 * 60 * 60);
            $values['abilities'][$super] = TRUE;
        }
    }
}


?>
