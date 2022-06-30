<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styleq.css">
    <title>afsdsdорма</title>

    <style>

        .error {
            border: 2px solid red;
        }
    </style>
</head>
<body>

<nav>
<ul>
    <li><a href="#form" title = "Форма">Форма</a></li>
    <li>
        <?php
        if(!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']))
            print('<a href="./?quit=1" title = "Выйти">Выйти</a>');
        else
            print('<a href="login.php" title = "Войти">Войти</a>');
        ?>
    </li>
       <li>
        <a href="admin.php" title = "Я-администратор">Я-администратор</a>
    </li>
</ul>
</nav>


<?php
if (!empty($messages)) {
    print('<div id="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
        print($message);
    }
    print('</div>');
}

// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>



<form action="." method="POST">
    <?php
          if (!empty($token)) {
            print ('<input type="hidden" name="csrftoken" value="'.$token.'"/>');
          }
    ?>

    <label>Ваше имя</label>
    <input name="fio" <?php if ($errors['fio']) {print 'class="error"';} ?> value="<?php print $values['fio']; ?>" />
    <br>

    <label>Ваш email</label>
    <input name="email" <?php if ($errors['email']) {print 'class="error"';} ?> value="<?php print $values['email']; ?>" type="email">
    <br>

    <p>Ваш го рождения</p>
    <select name="bd" >
        <?php for ($i = 1900; $i < 2021;$i++) {
            print('<option value="');
            print($i);
            if ($values['bd'] == $i) print('" selected="selected">');
            else print('">');
            print($i);
            print('</option>');}?>
    </select>

    <br>



    <p>Ваш пол</p>
    <label class="radio">
        <input type="radio" name="sex" value="M" <?php if ($values['sex'] == 'M') {print 'checked';} ?> >
        M
    </label>
    <label class="radio">
        <input type="radio" name="sex" value="F" <?php if ($values['sex'] == 'F') {print 'checked';} ?> >
        Ж
    </label>
    <?php if ($errors['sex']) {print 'class="error"';} ?>
    <br>

    <p>Количество конечностей</p>
    <label class="radio">
        <input type="radio" name="limbs" value="2" <?php if ($values['limbs'] == '2') {print 'checked';} ?>>
        2
    </label>
    <label class="radio">
        <input type="radio" name="limbs" value="4" <?php if ($values['limbs'] == '4') {print 'checked';} ?>>
        4
    </label>
    <label class="radio">
        <input type="radio" name="limbs" value="6" <?php if ($values['limbs'] == '6') {print 'checked';} ?>>
        6
    </label>
    <label class="radio">
        <input type="radio" name="limbs" value="8" <?php if ($values['limbs'] == '8') {print 'checked';} ?>>
        8
    </label>
    <label class="radio">
        <input type="radio" name="limbs" value="10" <?php if ($values['limbs'] == '10') {print 'checked';} ?>>
        10
    </label>
    <br>
    <?php if ($errors['limbs']) {print 'class="error"';} ?>
    <br>

    <select  name="abilities[]" multiple  <?php if ($errors['abilities']) {print 'class="error"';} ?> >

        <option value="0" <?php if ($values['abilities'][0]) {print 'selected';} ?>>Бессмертие</option>
        <option value="1" <?php if ($values['abilities'][1]) {print 'selected';} ?>>Прохождение сквозь стен</option>
        <option value="2" <?php if ($values['abilities'][2]) {print 'selected';} ?>>Левитация</option>
        <option value="3" <?php if ($values['abilities'][3]) {print 'selected';} ?>>Невидимость</option>

    </select>
    <br>
    <br>

    <p>Ваша биография</p>
    <textarea  <?php if ($errors['bio']) {print 'class="error"';} ?> value="<?php print $values['bio']; ?>" name="bio" placeholder="Your biography" rows=10 cols=30 ><?php print $values['bio']; ?></textarea>
    <br>

    <input type="checkbox" name="accept" value="1">Принять
    <br>
    <input type="submit" value="Отправить">
</form>
