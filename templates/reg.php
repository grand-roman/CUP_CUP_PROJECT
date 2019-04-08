<h2 class="content__main-heading">Регистрация аккаунта</h2>

<form class="form" action="" method="post">
  <div class="form__row">
    <?php $classname = isset($errors_user['email']) ? "form__input--error" : "";
        $value = isset($user_reg['email']) ? $user_reg['email'] : ""; ?>
    <label class="form__label" for="email">E-mail <sup>*</sup></label>

    <input class="form__input <?=$classname ?>" type="text" name="signup[email]" id="email" value="<?=$value ?>" placeholder="Введите e-mail">
    <?php if (isset($errors_user['email'])): ?>
    <p class="form__message"><?=$errors_user['email'] ?></p>
    <?php endif; ?>
  </div>

  <div class="form__row">
    <?php $classname = isset($errors_user['password']) ? "form__input--error" : "";
        $value = isset($user_reg['password']) ? $user_reg['password'] : ""; ?>
    <label class="form__label" for="password">Пароль <sup>*</sup></label>

    <input class="form__input <?=$classname ?>" type="password" name="signup[password]" id="password" value="<?=$value ?>" placeholder="Введите пароль">
    <?php if (isset($errors_user['password'])): ?>
    <p class="form__message"><?=$errors_user['password'] ?></p>
    <?php endif; ?>
  </div>

  <div class="form__row">
    <?php $classname = isset($errors_user['name']) ? "form__input--error" : "";
        $value = isset($user_reg['name']) ? $user_reg['name'] : ""; ?>
    <label class="form__label" for="name">Имя <sup>*</sup></label>

    <input class="form__input <?=$classname ?>" type="text" name="signup[name]" id="name" value="<?=$value ?>" placeholder="Введите имя">
    <?php if (isset($errors_user['name'])): ?>
    <p class="form__message"><?=$errors_user['name'] ?></p>
    <?php endif; ?>
  </div>

  <div class="form__row form__row--controls">
    <?php if (!empty($errors_user)): ?>
    <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
    <?php endif; ?>

    <input class="button" type="submit" name="" value="Зарегистрироваться">
  </div>
</form>