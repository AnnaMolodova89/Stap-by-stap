<?php

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];


$nav = [
   'index.php' => 'Главная',
   'rules.php' => 'Основные правила',
   'products.php' => 'Список продуктов',
   'register.php' => 'Регистрация',
   'users.php' => 'Личный кабинет'
];

?>



<div class="header"> 
   <div class="brand_box">
   <h1>STEP BY STEP</h1>
   </div>
   <img src="images/slider-img.jpg" alt="Ананасы">

   
<section class = "menu">
   <div class="container">
   <div class="button">
    
   <?php foreach ($nav as $key => $value):?> 
         <a href="<?php echo $key ?>" <?php echo $uri === $key ? 'style="color:white"':'' ?> ><?php echo $value ?></a> 
          <?php endforeach; ?>
     
   </div>
   </div>
</section>
</div>