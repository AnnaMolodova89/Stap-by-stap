<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $reg_email = htmlspecialchars(trim($_POST['reg_email']));

    if(empty($reg_email)){//если емейл не введен
        $error_reg_email = 'Введите адрес эл.почты';
    }else{//если адрес указан
        try {//подключаемся к базе и делаем выборку по указанному емейлу
            $host = 'localhost';
            $db_name = 'users';
            $login_db = 'root';
            $password = '';
   
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $login_db, $password);

            $query = 'SELECT email FROM users WHERE email = ?';
            $result = $pdo->prepare($query);
            $result->execute(array($reg_email));
            $result = $result->rowCount();  
            
            if ($result) {//если указан емейл, кот.есть в базе,генерируем новый пароль
                //генерируем случайный пароль
                $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
                $array = str_split($chars);
                shuffle($array);
                $str = implode($array);
                $password = substr($str, 0, 15);
                //отправляем письмо с паролем на указанный пользователем емейл
                $mail = 'Вы запросили восстановление пароля.
                Войти на сайт вы можете используя указанный пароль: '. $password . '
                Для обеспечения безопасности рекомендуем после входа в личный кабинет изменить пароль';
                mail($reg_email, 'Восстановление пароля', $mail);

                //хэшируем пароль и записываем его в бд
                $password = password_hash($password, PASSWORD_DEFAULT);
                $query = 'UPDATE users SET password = ? WHERE email = ?';
                $result = $pdo->prepare($query);
                $result->execute(array($password, $reg_email));

                if ($result) {
                    $error_reg_email = 'При восстановлении пароля произошла ошибка, попробуйте позже';
                } else {
                    $error_reg_email = 'На указанный адрес электронной почты был отправлен пароль
                    для восстановления доступа.
                    <a href = "enter.php">Перейти на страницу входа</a>';
                }

            }else{
                $error_reg_email = 'Указанный адрес электронной почты не зарегистрирован';
            }
            
        } catch(PDOException $e){
            echo $e->getMessage();
            exit();
        }

    }
}


?>

<?php $title = 'Восстановление пароля'; ?>
<?php include 'components/header.php'; ?>

<body>

<?php include 'components/nav.php'; ?>

<div id="page">
    <form action="" method="POST">
        <label for="reg_email">Введите адрес электронной почты, указанный при регистрации</label><br>
        <input type="text" name="reg_email"><br>
        <input type="submit" id="send" value="Восстановить пароль">
        <p class = "error"><?php echo $error_reg_email;?></p>
</form>
</div>
<?php include 'components/footer.php'; ?>