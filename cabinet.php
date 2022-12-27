<?php
session_start();
$login = $_SESSION['valid_user'];



require 'DbConnect.php';
$pdo = DbConnect::getConnection();

//выборка данных по логину
$query = "SELECT login, first_name, last_name, email, password, image
    FROM users
    WHERE login = ?";
$result = $pdo->prepare($query);
$result->execute([$login]);
$user = $result->fetch();





if (isset($_POST['edit_password'])) {//изменение пароля
    $edit_password = htmlspecialchars(trim($_POST['edit_password']));

    if (!preg_match('/^.{6,}$/ui', $edit_password)) {
        header('Location: cabinet.php?error_edit_password=Пароль должен буть не короче шести символов');
    }else{
        $edit_password = password_hash($edit_password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = ? WHERE login = ?";
        $result = $pdo->prepare($query);
        $result->execute(array($edit_password, $user['login']));
        header('Location: cabinet.php');
        exit();
    }
}

?>



<?php $title = 'Личный кабинет'; ?>
<?php include 'components/header.php'; ?>

<body>

<?php include 'components/nav.php'; ?>



<div id="main">
    <h3>Здравствуйте, <?php echo $user['first_name'] . ' ' . $user['last_name'];?></h3>
<div class="field"> <!-- блок для вывода фото -->    
    <img style = "width: 300px" src="<?php echo $user['image'];?>">
</div>
<div class="field"> <!-- блок для вывода логина -->
    <p>Ваш логин: <?php echo $user['login'];?></p>
</div>
<div class="field"><!-- блок для вывода имени -->
    <p>Ваше имя: <?php echo $user['first_name'];?></p>
</div>
<div class="field"><!-- блок для вывода фамилии -->
    <p>Ваша фамилия: <?php echo $user['last_name'] ?></p>
</div>
<div class="field"><!-- блок для вывода емейла -->
    <p>Ваша почта: <?php echo $user['email'] ?></p>
</div>
<div class="field"><!-- изменение пароля  -->
            <a href="?edit_password">Изменить пароль</a>
            <span><?php echo $_GET['error_edit_password'] ?? '';?></span>

            <?php // отображаем форму для изменения пароля - 1 шаг
                if( isset($_GET['edit_password']) ){ // если нажата ссылка edit_password
                    // показываем форму
                    //d($_GET);
                    echo <<<_HTML_
                        <form action="?" method="POST">
                        <div class = "newPassword">
                                <label for="edit_password" class = "edit_password">Введите новый пароль</label>
                                <input type="password" name="edit_password">
                            </div>
                            <input type="submit" value="Изменить">
                        </form>                 
_HTML_;
                }
            ?>
        </div>




<a href = "index.php">На главную</a>
<a href = "exit.php">Выйти</a>

</div> <!-- конец блока main -->

<?php include 'components/footer.php'; ?>