<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Массив для временного хранения сообщений пользователю.
    $messages = array();

    // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
    // Выдаем сообщение об успешном сохранении.
    if (!empty($_COOKIE['save'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        // Выводим сообщение пользователю.
        $messages[] = 'Спасибо, результаты сохранены.';
        // Если в куках есть пароль, то выводим сообщение.
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
    }

    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['bd'] = !empty($_COOKIE['bd_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['sex'] = !empty($_COOKIE['sex_error']);
    $errors['limbs'] = !empty($_COOKIE['limbs_error']);
    $errors['accept'] = !empty($_COOKIE['accept_error']);
    $errors['abilities'] = !empty($_COOKIE['abilities_error']);


    // Выдаем сообщения об ошибках.
    if (!empty($errors['fio'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('fio_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Заполните имя.</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Заполните email.</div>';
    }

    if ($errors['bd']) {
        setcookie('bd_error', '', 100000);
        $messages[] = '<div class="error">Заполните date.</div>';
    }

    if ($errors['bio']) {
        setcookie('bio_error', '', 100000);
        $messages[] = '<div class="error">pull bio.</div>';
    }

    if ($errors['sex']) {
        setcookie('sex_error', '', 100000);
        $messages[] = '<div class="error">pull sex.</div>';
    }

    if ($errors['limbs']) {
        setcookie('limbs_error', '', 100000);
        $messages[] = '<div class="error">pull limbs.</div>';
    }

    if ($errors['abilities']) {
        setcookie('abilities_error', '', 100000);
        $messages[] = '<div class="error">abilities error.</div>';
    }

    if ($errors['accept']) {
        setcookie('accept_error', '', 100000);
        $messages[] = '<div class="error">accept error.</div>';
    }


    // Складываем предыдущие значения полей в массив, если есть.
    // При этом санитизуем все данные для безопасного отображения в браузере.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['bd'] = empty($_COOKIE['bd_value']) ? '' : strip_tags($_COOKIE['bd_value']);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : strip_tags($_COOKIE['sex_value']);
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : strip_tags($_COOKIE['limbs_value']);
    $values['accept'] = empty($_COOKIE['accept_value']) ? '' : strip_tags($_COOKIE['accept_value']);
    $values['abilities'] = array();
    $values['abilities'][0] = empty($_COOKIE['abilities_value_1']) ? '' : $_COOKIE['abilities_value_1'];
    $values['abilities'][1] = empty($_COOKIE['abilities_value_2']) ? '' : $_COOKIE['abilities_value_2'];
    $values['abilities'][2] = empty($_COOKIE['abilities_value_3']) ? '' : $_COOKIE['abilities_value_3'];
    $values['abilities'][3] = empty($_COOKIE['abilities_value_4']) ? '' : $_COOKIE['abilities_value_4'];

    session_start();
    if (!empty($_GET['quit'])) {
        session_destroy();
        $_SESSION['login'] = '';
    }


    $token = '';
    // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
    // ранее в сессию записан факт успешного логина.
    if (!empty($_SESSION['login'])) {

        $user = 'u40077';
        $pass_db = '3053723';
        $db = new PDO('mysql:host=localhost;dbname=u40077', $user, $pass_db, array(PDO::ATTR_PERSISTENT => true));
        $stmt1 = $db->prepare('SELECT fio, email, bd, sex, limbs, bio FROM formtab5 WHERE form_id = ?');
        $stmt1->execute([$_SESSION['uid']]);
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);


        $values['abilities'][0] = 0;
        $values['abilities'][1] = 0;
        $values['abilities'][2] = 0;
        $values['abilities'][3] = 0;


        $stmt2 = $db->prepare('SELECT abid FROM usertab WHERE form_id = ?');
        $stmt2->execute([$_SESSION['uid']]);
        while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $values['abilities'][$row['abid']] = TRUE;
        }

        include('sel.php');

        // и заполнить переменную $values,
        // предварительно санитизовав.
        printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);


        $salt = substr(md5(uniqid()), 0, 5);
        $token = $salt . ':' . substr(hash("sha256", $salt . $_SESSION['secret']), 0, 20);

    }

    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
    // Проверяем ошибки.
    $errors = FALSE;
    if (!preg_match("#^[aA-zZ0-9]+$#",($_POST['fio']))) {
        // Выдаем куку на день с флажком об ошибке в поле fio.
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
    }
    $values['fio'] = $_POST['fio'];

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
    }
    $values['email'] = $_POST['email'];

    if ($_POST['bd'] < 1900 & $_POST['bd'] > 2022) {
        setcookie('bd_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('bd_value', $_POST['bd'], time() + 30 * 24 * 60 * 60);
    }
    $values['bd'] = $_POST['bd'];

    if (!preg_match('/^[MF]$/',$_POST['sex'])) {
        setcookie('sex_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60);
    }
    $values['sex'] = $_POST['sex'];

    if (!($_POST['limbs'] == 2 | $_POST['limbs'] == 4 | $_POST['limbs'] == 6 | $_POST['limbs'] == 8 | $_POST['limbs'] == 10 )) {
        setcookie('limbs_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60);
    }
    $values['limbs'] = $_POST['limbs'];

    if (empty($_POST['bio'])) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
    }
    $values['bio'] = $_POST['bio'];

    if (!isset($_POST['accept'])) {
        setcookie('accept_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }

    setcookie('abilities_value_1', '0', time() + 30 * 24 * 60 * 60);
    setcookie('abilities_value_2', '0', time() + 30 * 24 * 60 * 60);
    setcookie('abilities_value_3', '0', time() + 30 * 24 * 60 * 60);
    setcookie('abilities_value_4', '0', time() + 30 * 24 * 60 * 60);

    if (empty($_POST['abilities'])) {
        setcookie('abilities_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        foreach ($_POST['abilities'] as $super) {
            if (!($_POST['abilities'][$super] == 0 | $_POST['abilities'][$super] == 1 | $_POST['abilities'][$super] == 2 | $_POST['abilities'][$super] == 3)) {
                setcookie('abilities_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
            else {
                setcookie('abilities_value_' . $super, '1', time() + 30 * 24 * 60 * 60);
                $values['abilities'][$super] = TRUE;
            }
        }
    }

// *************
// Сохранить в Cookie признаки ошибок и значения полей.
// *************


    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: index.php');
        exit();
    }
    else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('bd_error', '', 100000);
        setcookie('abilities_error', '', 100000);
        setcookie('bio_error', '', 100000);
        setcookie('sex_error', '', 100000);
        setcookie('limbs_error', '', 100000);
        setcookie('accept_error', '', 100000);

    }

    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
    if (!empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])) {

        $sentToken = $_POST['csrftoken'];
        $sentTokenArray = explode(':', $sentToken, 2);
        $salt = $sentTokenArray[0];
        $token = $salt . ':' . substr(hash("sha256", $salt . $_SESSION['secret']), 0, 20);
        if ($token != $sentToken) {
            print ("error=notoken");
        }

        // TODO: перезаписать данные в БД новыми данными,
        // кроме логина и пароля.
        include("foo.php");
        up($values['fio'], $values['email'], $values['bd'], $values['sex'], $values['limbs'], $values['bio'], $_SESSION['uid'], $_POST['abilities']);
    }


    else {
        // Генерируем уникальный логин и пароль.
        // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
        $id = uniqid();
        $hash = md5($id);
        $login = substr($hash, 0, 10);
        $pass = substr($hash, 10, 15);
        // Сохраняем в Cookies.
        setcookie('login', $login);
        setcookie('pass', $pass);

        include("bd.php");

        $stmt1 = $db->prepare("INSERT INTO formtab5 SET fio = ?, email = ?, bd = ?, 
      sex = ? , limbs = ?, bio = ?, login = ?, pass = ?");
        $stmt1 -> execute([$values['fio'], $values['email'], $values['bd'],
            $values['sex'], $values['limbs'], $values['bio'], $login, $pass]);
        $stmt2 = $db->prepare("INSERT INTO usertab SET form_id = ?, abid = ?");
        $id = $db->lastInsertId();
        foreach ($values['abilities'] as $s)
            $stmt2 -> execute([$id, $s]);

    }

    // Сохраняем куку с признаком успешного сохранения.
    setcookie('save', '1');

    // Делаем перенаправление.
    header('Location: ./');
}