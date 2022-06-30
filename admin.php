
<?php
  if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    !empty($_GET['logout'])) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="Enter login and password"');
    if (!empty($_GET['logout']))
      header('Location: admin.php');
    print('<h1>401 Требуется авторизация</h1></div></body>');
    exit();
  }

include("bd.php");
  
  $login = trim($_SERVER['PHP_AUTH_USER']);
  $pass =  trim($_SERVER['PHP_AUTH_PW']);
  $stmtCheck = $db->prepare('SELECT admin_pass FROM admin WHERE admin_login = ?');
  $stmtCheck->execute([$login]);
  $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

  if ($row == false || $row['admin_pass'] != $pass) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="Invalid login or password"');
    print('<h1>401 Неверный логин или пароль</h1>');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleq.css">
</head>
<body>
    <section>
        <h2>Администрирование</h2>
        <a href="./?quit=1">Выйти</a>
    </section>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    $stmtCount = $db->prepare('SELECT abname, count(fa.form_id) AS amount FROM abils AS ab LEFT JOIN usertab AS fa ON ab.abid = fa.abid GROUP BY ab.abid');
    $stmtCount->execute();
    print('<section>');

    while($row = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
        print('<b>' . $row['abname'] . '</b>: ' . $row['amount'] . '<br/>');
    }
    print('</section>');

    $stmt1 = $db->prepare('SELECT form_id, fio, email, bd, sex, limbs, bio, login FROM formtab5');
    $stmt2 = $db->prepare('SELECT abid FROM usertab WHERE form_id = ?');
    $stmt1->execute();

    while($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        print('<section>');
        print('<h2>' . $row['login'] . '</h2>');
        $abilities = [false, false, false, false];
        $stmt2->execute([$row['form_id']]);
        while ($superrow = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $abilities[$superrow['abid']] = true;
        }
        foreach ($row as $key => $value)
            if (is_string($value))
                $row[$key] = strip_tags($value);

        include('sel.php');
        include('adminform.php');
        print('</section>');
    }

}


else {

    if (array_key_exists('delete', $_POST)) {
        include("foo.php");
        del();
        header('Location: admin.php');
        exit();
    }



    $values = [];
    $values['fio'] = $_POST['fio'];
    $values['email'] = $_POST['email'];
    $values['bd'] = $_POST['bd'];
    $values['bio'] = $_POST['bio'];
    $values['sex'] = $_POST['sex'];


    $errors = FALSE;
    if (!preg_match("#^[aA-zZ0-9]+$#",($_POST['fio']))) {
        $errors = TRUE;
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors = TRUE;
    }

    if (!preg_match('/^[1900-2022]$/',$_POST['bd'])) {
        $errors = TRUE;
    }

    if (!preg_match('/^[MF]$/',$_POST['sex'])) {
        $errors = TRUE;
    }



    if (empty($_POST['bio'])) {
        $errors = TRUE;
    }

    if (empty($_POST['abilities'])) {
        $errors = TRUE;
    }




    if (array_key_exists('update', $_POST)) {

        include("foo.php");

        up($values['fio'], $values['email'], $values['bd'], $values['sex'], $_POST['limbs'], $values['bio'], $_POST['uid'], $_POST['abilities']);

        header('Location: admin.php');
        exit();
    }
}


?>
</body>

