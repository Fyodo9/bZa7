<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
    // Если есть логин в сессии, то пользователь уже авторизован.
    // TODO: Сделать выход (окончание сессии вызовом session_destroy()
    //при нажатии на кнопку Выход).
    // Делаем перенаправление на форму.
    header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['nologin']))
        print("<div>Пользователя с таким логином не существует</div>");
    if (!empty($_GET['wrongpass']))
        print("<div>Неверный пароль!</div>");

    ?>

    <form action="" method="post">
        <input name="login" />
        <input name="pass" />
        <input type="submit" value="Войти" />
    </form>

    <?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

    $user = 'u40077';
    $pass_db = '3053723';
    $db = new PDO('mysql:host=localhost;dbname=u40077', $user, $pass_db, array(PDO::ATTR_PERSISTENT => true));
    $stmt1 = $db->prepare('SELECT form_id, pass FROM formtab5 WHERE login = ?');
    $stmt1->execute([$_POST['login']]);
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);


    if (!$row) {
        header('Location: ?nologin=1');
        exit();
    }


    if ($row['pass'] != $_POST['pass']) {
        header('Location: ?wrongpass=1');
        exit();
    }

    $_SESSION['login'] = $_POST['login'];
    // Записываем ID пользователя.
    $_SESSION['uid'] = $row['form_id'];
    $_SESSION['secret'] = md5(uniqid());

    // Делаем перенаправление.
    header('Location: ./');

}


