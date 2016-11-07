<?php

require_once 'base.template.php';

$session = \SeedStars\Session::getInstance();

//fetch all errors
$allErrors = $session->get("errors");

$fullNameErrors = $allErrors['fullname'];
$emailErrors = $allErrors['mail'];
$userNameErrors = $allErrors['username'];
$passwordErrors = $allErrors['password'];

//then clear them out the session
$session->remove("errors");

?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


                <div class="col-md-3"></div>

                <div class="col-md-5">
                    <form method="POST" role="form">
                        <legend>Sign up to make use of our address book</legend>

                        <?php
                        if ($session->has("classic_error")) {
                            echo "<span class='error'>{$session->get('classic_error')}</span>";
                        }
                        ?>

                        <div class="form-group">
                            <label for="">Fullname</label>
                            <input type="text" class="form-control" name="fullname"
                                   placeholder="John Doe">
                            <span class="help-block">
                                Nice to meet you though!!!
                            </span>
                            <?php
                            if (isset($fullNameErrors)) {
                                echo "<span class=\"error\">{$fullNameErrors}</span>";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="">Username</label>
                            <input type="text" class="form-control" name="username"
                                   placeholder="fabpot">

                            <?php
                            if (isset($userNameErrors)) {
                                echo "<span class='error'> {$userNameErrors}</span>";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="">Email address</label>
                            <input type="email"
                                   class="form-control" name="mail"
                                   placeholder="<?= "john.doe@" . $_SERVER['HTTP_HOST'] ?>">

                            <?php
                            if (isset($emailErrors)) {
                                echo "<span class='error'>{$emailErrors}</span>";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password"
                                   class="form-control" name="password"
                                   autocomplete="off">
                            <?php
                            if (isset($passwordErrors)) {
                                echo "<span class='error'>{$passwordErrors}</span>";
                            }
                            ?>
                        </div>

                        <?= makeCsrfFormField() ?>
                        <button type="submit" class="btn btn-primary">Register account</button>
                    </form>

                </div>

                <div class="col-md-4">

                </div>

            </div>
        </div>
    </div>


<?php require_once 'footer.template.php';