 <?php

 // переменные для подключения к бд
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



/**
 *
 * функция для проверки данных пользователя
 *
 */
function validate_form(){
  //d($_POST);

  // массивы для данных пользователя и возможных ошибок ввода
  $errors = []; // ошибки
  $input = []; // данные

  // обезвреживание данных
  $input['login'] = htmlspecialchars( trim($_POST['login']) );
  $input['first_name'] = htmlspecialchars( trim($_POST['first_name']) );
  $input['last_name'] = htmlspecialchars( trim($_POST['last_name']) );
  $input['email'] = htmlspecialchars( trim($_POST['email']) );
  $input['password'] = htmlspecialchars( trim($_POST['password']) );
  $input['image'] = $_FILES['image'];

//  foreach ($_POST as $key => $value){
//    $input[$key] = htmlspecialchars((trim($_POST[$key])));
//  }


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

    try{
        $query = "SELECT login FROM users WHERE login = :login";
        $result = $GLOBALS['pdo']->prepare($query);
        $result->bindParam(':login', $login);
        $result->execute();
        $result = $result->rowCount();
    }catch(PDOException $e){
        print $e->getMessage();
        exit();
    }

    if($result){
        return "Такой логин уже занят";
    }
  }

  if(validate_login($input['login'])){
    $errors['login'] = validate_login($input['login']);
  }




  /**
   * функция для проверки имени
   */
  // объявляем функцию
  function validate_first_name($first_name){

    $regexp = "/^[а-яё]*$/ui"; //только русские буквы

    if( empty($first_name) ){// проверка на пустоту
      return 'Введите имя';
    }elseif( strlen($first_name) < 2 ){
      return 'Имя должно состоять не менее чем из двух букв';
    }elseif( !preg_match($regexp, $first_name) ){
      return 'Имя должно состоять только из русских букв';
    }
  }

  if(validate_first_name($input['first_name'])){
    $errors['first_name'] = validate_first_name($input['first_name']);
  }


  /**
   * функция для проверки фамилии
   */
  function validate_last_name($last_name){

    $regexp = "/^[а-яё]*$/ui"; //только русские буквы

    if( empty($last_name) ){// проверка на пустоту
      return 'Введите фамилию';
    }elseif( strlen($last_name) < 2 ){
      return 'Фамилия должна состоять не менее чем из двух букв';
    }elseif( !preg_match($regexp, $last_name) ){
      return 'Фамилия должна состоять только из русских букв';
    }
  }

  if(validate_last_name($input['last_name'])){
    $errors['last_name'] = validate_last_name($input['last_name']);
  }
  

  /**
   * функция для проверки емейла
   */
  function validate_email($email){

    $regexp = "/^.+@.+\.[a-z]+$/i";

    if(empty($email)){
      return 'Введите адрес электронной почты';
    }elseif( !preg_match($regexp, $email) ){
      return 'Адрес электронной почты введен в неверном формате';
    }

    try{
        $query = "SELECT email FROM users WHERE email = :email";
        $result = $GLOBALS['pdo']->prepare($query);
        $result->bindParam(':email', $email);
        $result->execute();
        $result = $result->rowCount();
    }catch(PDOException $e){
        print $e->getMessage();
        exit();
    }

    if($result){
        return "Этот адрес электронной почты уже зарегистрирован";
    }
}
if (validate_email($input['email'])){
    $errors['email'] = validate_email($input['email']);
}



  /**
   * функция для проверки пароля
   */
  function validate_password($password){
    $regexp = "/^.{6,}$/ui";

    if( empty($password) ){
      return 'Введите пароль';
    }elseif ( !preg_match($regexp, $password) ){
      return 'Пароль должен содержать не менее шести произвольных символов';
    }
  }
  if(validate_password($input['password'])){
    $errors['password'] = validate_password($input['password']);
  }


  /**
   * функция изображения
   */
  function validate_image($image){
    

    if( empty($image['name']) ){
      return 'Фото не выбрано';
    }elseif($image['size'] > 5242880){
      return 'Размер фото должен быть не более 5Мб';
    }elseif(($image['type'] !== 'image/png') && ($image['type'] !== 'image/jpeg')){
      return 'Вы можете загружать фото только в формате jpg или png';
    }elseif($image['error'] !== 0){
      return 'При загрузке файла произошла ошибка';  
    }  
}
if(validate_image($input['image'])){
    $errors['image'] = (validate_image($input['image']));
}


//возвращаем из функции validate_form() двумерный массив с ошибками(если есть) и данными
  return [$errors, $input];
}// конец функции validate_form()


//функция отправки данных
/**
 * process_form - добавление данных в бд, старт сессии
 *  'first_name', 'last_name', 'login', 'email', 'password', 'image'
 */
function process_form($input){
  // шифрование пароля
  $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

  // перемещение картинки
  $input['image']['name'] = 'images/'.$input['image']['size'].'_'.$input['image']['name'];
  move_uploaded_file($input['image']['tmp_name'], $input['image']['name']);

  // добавление всех данных в бд
  $query = "INSERT INTO users (login, first_name, last_name, email, password, image)
                            VALUES(?, ?, ?, ?, ?, ?)";
  $result = $GLOBALS['pdo']->prepare($query);
  $result->execute([
    $input['login'],
    $input['first_name'],
    $input['last_name'],
    $input['email'],
    $input['password'],
    $input['image']['name']
    ]);


  // старт сессии, добавление данных в сессию
  session_start();
  $_SESSION['valid_user'] = $input['login'];
  $_SESSION['first_name'] = $input['first_name'];
  $_SESSION['last_name'] = $input['last_name'];

  header('Location: /');
}
?>



<?php
//функция для отображения формы
function show_form($errors = [], $input = []){

  // массив с полями
  $fields = ['first_name', 'last_name', 'login', 'email', 'password', 'image'];

  foreach ($fields as $field) {// перебираем массив с полями
    if( !isset($errors[$field]) ) $errors[$field] = '';// если элемент с указанным полем отсутствует, присваиваем
    // пустую строку
    if( !isset($input[$field]) ) $input[$field] = '';
  }


  $title = 'Регистрация'; 
  include 'components/header.php';
  include 'components/nav.php'; 
    echo<<<_HTML_
    <div class = "page">
    <div id = "page">
    
    <form method="POST" action="$_SERVER[PHP_SELF]" enctype="multipart/form-data">

    <p>Регистрация:</p>
    
    <div class="flex">
    <label for="login">Логин:</label>
    <input type="text" name="login" class="long" value="$input[login]" placeholder="Введите логин латинсими буквами">
    <span class="error">$errors[login]</span>
    </div>
    
    <div class="flex">
    <label for="first-name">Имя:</label>
    <input type="text" name="first_name" class="long" value="$input[first_name]" placeholder="Введите имя русскими буквами"
    value = "$input[first_name]">
    <span class="error">$errors[first_name]</span>
    </div>
    
    
    <div class="flex">
    <label for="last_name">Фамилия</label>
    <input type="text" name="last_name" class="long" value="$input[last_name]" placeholder="Введите фамилию русскими буквами"
    value = "$input[last_name]">
    <span class="error">$errors[last_name]</span>
    </div>
    
    
    <div class="flex">
    <label for="email">Эл.почта</label>
    <input type="text" name="email" class="short" value="$input[email]" placeholder="Укажите ваш электронный адрес" value = "$input[email]">
    <span class="error">$errors[email]</span>
    </div>
    
    <div class="flex">
    <label for="password">Пароль:</label>
    <input type="password" name="password" class="short" placeholder="Введите пароль">
    <span class="error">$errors[password]</span>
    </div>
    
    <div class="flex">
    <label for="image">Фото:</label>
    <input type="file" name="image" class="long" placeholder="Загрузите свежее фото">
    <span class="error">$errors[image]</span>
    </div>
    
    
    <input type="submit" value="Отправить данные">                                           
            
        </div>
        </div>
    </body>
    </html>
    
    _HTML_;


    } //конец функции отображения формы
    
    
    

include 'components/footer.php';


    

   
    


   

    

   

   
    
    