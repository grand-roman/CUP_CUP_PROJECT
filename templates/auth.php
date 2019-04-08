<h2 class="content__main-heading">Вход на сайт</h2>

<form class="form" action="auth.php" method="post">
    <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?=(isset($errors_log['email']) ? "form__input--error" : "");?> " type="text" name="email" id="email" value="<?=($user_log['email'] ?? "");?>" placeholder="Введите e-mail">

        <p class="form__message"><?=($errors_log['email'] ?? "");?></p>
    </div>

    <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?=(isset($errors_log['password']) ? "form__input--error" : "");?>" type="password" name="password" id="password" value="<?=($user_log['password'] ?? "");?>" placeholder="Введите пароль">

        <p class="form__message"><?=($errors_log['password'] ?? "");?></p>
    </div>

    <div class="form__row form__row--controls">
        <p class="error-message"><?=(!empty($errors_log) ? "Пожалуйста, исправьте ошибки в форме" : "");?></p>

        <input class="button" type="submit" name="" value="Войти">
    </div>