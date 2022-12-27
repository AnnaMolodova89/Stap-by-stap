<?php
session_start();
?>
<?php $title = 'Личный кабинет'; ?>
<?php include 'components/header.php'; ?>
<?php include 'components/nav.php'; ?>
<body>

<!-- <div class = "text"> -->

<h3> В личном кабинете у вас будет новое меню каждый день!</h3>
<h3> При регистрации обязательно приложить фото, сделанное сегодня!</h3>

<div id="main">
    <?php
      if( isset($_SESSION['valid_user']) ){// если пользователь авторизован
        // показываем какую-то персональную инфу
        echo "<h3>Добро пожаловать, $_SESSION[first_name] $_SESSION[last_name]</h3>";
        echo '<a href="cabinet.php">Личные данные</a><br>';
        echo '<a href="exit.php">Выход</a>';
        //d($_SESSION['valid_user']);
      }else{ // если пользователь не авторизован
        // показываем ссылки на регистрацию и вход
        echo '<a href="enter.php">Вход</a><br>';
        echo '<a href="register.php">Регистрация</a>';
      }

?>

</div>

<?php include 'components/footer.php'; ?>