<?php $title = 'Регистрация'; ?>
<?php include 'components/header.php'; ?>

<body>

<?php include 'components/nav.php'; ?>


 <?php
 
require 'DbConnect.php';
$pdo = DbConnect::getConnection();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){ // если данные отправлены методом POST
    // проверяем данные
    list($errors, $input) = validate_form();// забираем данные и ошибки из функции проверки

    if($errors){ // если ошибки есть
      // показываем форму и выводим ошибки
      show_form($errors, $input);
    }else{ // если ошибок нет
      // отправляем форму с заполненными данными
      process_form($input);
    }
}else{ // если сайт загружен впервые
    // отображаем форму для заполнения
    show_form();
}

function validate_form(){

    $errors = []; // ошибки
    $input = [];

    $input['login'] = htmlspecialchars( trim($_POST['login']) );
    $input['password'] = htmlspecialchars( trim($_POST['password']) );




    /**
   * функция для проверки логина
   */
  function validate_login($login){

    $regexp = "/^[a-z][a-z0-9]*$/i"; //только латинские буквы и цифры
                                     //и должны начинаться с буквы
    if( empty($login)){ //если отправлено пустое поле
      return 'Введите логин';
    }elseif( strlen($login) < 3){// длина меньше 3 байт
      return 'Логин должен быть не короче трех символов';
    }elseif( !preg_match($regexp, $login) ){//проверка на соотв.рег.выражению
      return 'Логин должен содержать латинские буквы или цифры и должен начинаться с буквы';
    }

    try{ //проверка логина по бд
        $query = "SELECT login FROM users WHERE login = :login";
        $result = $GLOBALS['pdo']->prepare($query);
        $result->bindParam(':login', $login);
        $result->execute();
        $result = $result->rowCount();
    }catch(PDOException $e){
        print $e->getMessage();
        exit();
    }

    if(!$result){ //проверка на уникальность
        return "Такой логин не зарегистрирован";
    }
  }

  if(validate_login($input['login'])){ //запуск проверки
    $errors['login'] = validate_login($input['login']); //если есть ошибка заносим в массив
  }

  /**
   * функция для проверки пароля
   */
  function validate_password($password, $login){
    $regexp = "/^.{6,}$/ui";

    if( empty($password) ){
      return 'Введите пароль';
    }elseif ( !preg_match($regexp, $password) ){
      return 'Пароль должен содержать не менее шести произвольных символов';
    }

    try{ 
        $query = "SELECT password FROM users WHERE login = :login";
        $result = $GLOBALS['pdo']->prepare($query);
        $result->bindParam(':login', $login);
        $result->execute();
        $result = $result->fetch();
    }catch(PDOException $e){
        print $e->getMessage();
        exit();
    }

    $hash = password_verify($password, $result['password']);
    if(!$hash){
        return "Пароль неверен";
    }
  }
  if(validate_password($input['password'], $input['login'])){
    $errors['password'] = validate_password($input['password'], $input['login']);
  }

  return array($errors, $input);

}

//функция отправки данных
function process_form($input){

    session_start();
    $_SESSION['valid_user'] = $input['login'];
    header('Location: users.php');
}

function show_form($errors = [], $input = []){

  // массив с полями
  $fields = ['first_name', 'last_name', 'login', 'email', 'password', 'image'];

  foreach ($fields as $field) {// перебираем массив с полями
    if( !isset($errors[$field]) ) $errors[$field] = '';// если элемент с указанным полем отсутствует, присваиваем
    // пустую строку
    if( !isset($input[$field]) ) $input[$field] = '';
  }

    echo<<<_HTML_
    <div class = "page">
    <div id = "page">
    
    <form method="POST" action="$_SERVER[PHP_SELF]">

    <p>Вход:</p>
    
    <div class="flex">
    <label for="login">Логин:</label>
    <input type="text" name="login" class="short" value = "$input[login]">
    <span class="error">$errors[login]</span>
    </div>
    
    <div class="flex">
    <label for="password">Пароль:</label>
    <input type="password" name="password" class="short">
    <span class="error">$errors[password]</span>
    </div>

    
    
    <input type="submit" id="send" value="Отправить">

   
   
    </form>

    </div>
    </div>
    </body>
    </html>
    
    _HTML_;

    

}
include 'components/footer.php';



    






