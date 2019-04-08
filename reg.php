<?php

require_once("functions.php");
require_once("init.php");
error_reporting(E_ALL);

$errors_user = [];
$user_reg = [];

$reg_content = include_template('reg.php', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_reg = $_POST['signup'] ?? [];
    $req_fields = ['email', 'password', 'name'];

    if (isset($user_reg['email']) && !filter_var($user_reg['email'], FILTER_VALIDATE_EMAIL)) {
      $errors_user['email'] = "E-mail введён некорректно";
    }

    foreach ($req_fields as $required_field) {
        if (empty($user_reg[$required_field])) {
            $errors_user[$required_field] = 'Обязательно заполните это поле';
        }
    }

    $res = repeatEmail($user_reg['email']);


	if (!empty($res)) {
	    $errors_user['email'] = 'Пользователь с этим email уже зарегистрирован';
	}


    if (empty($errors_user)) {
    
	    $password = password_hash($user_reg['password'], PASSWORD_DEFAULT);   
	    
	    regUser(
			$user_reg['email'],
			$user_reg['name'],
			$password);
		header("Location: /");
	       
	}
     
}

$reg_content = include_template('reg.php', [
  "errors_user" => $errors_user,
  "user_reg" => $user_reg
]);

$reg_layout = include_template('reg-layout.php', [
    'content'    => $reg_content,
    'title'      => 'Регистрация'
]);
print($reg_layout);

?>