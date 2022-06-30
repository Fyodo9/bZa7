
<?php

function up($fio, $email, $bd, $sex, $limbs, $bio, $uid, $abilities)
{
    include("bd.php");
    $stmt1 = $db->prepare('UPDATE formtab5 SET fio=?, email=?, bd=?, sex=?, limbs=?, bio=? WHERE form_id = ?');
    $stmt1->execute([$fio, $email, $bd, $sex, $limbs, $bio, $uid]);

    $stmt2 = $db->prepare('DELETE FROM usertab WHERE form_id = ?');
    $stmt2->execute([$uid]);

    $stmt3 = $db->prepare("INSERT INTO usertab SET form_id = ?, abid = ?");
    foreach ($abilities as $s)
        $stmt3->execute([$uid, $s]);
}



function del()
{
    include("bd.php");
    $stmt1 = $db->prepare('DELETE FROM usertab WHERE form_id = ?');
    $stmt1->execute([$_POST['uid']]);
    $stmt2 = $db->prepare('DELETE FROM formtab5 WHERE form_id = ?');
    $stmt2->execute([$_POST['uid']]);

}

?>

