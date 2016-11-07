<?php

require_once 'base.template.php';

$session = \SeedStars\Session::getInstance();
$errors = $session->get("errors");
$session->remove("errors");
$failed = $session->get("failed");
$session->remove("failed");
?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


                <div class="col-md-3"></div>

                <div class="col-md-4">

                    <?php
                    if ($failed) {
                        echo '<div class="alert alert-info">
                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        	<strong>Whoops</strong> Invalid credentials
                        </div>';
                    }
                    ?>
                    <form method="POST" role="form">
                        <legend>Login Form</legend>

                        <div class="form-group">
                            <label for="">
                                Username
                            </label>
                            <input type="text" class="form-control" name="username"
                                   placeholder="fabpot">
                            <?php
                            if (isset($errors['username'])) {
                                echo "<span class='error'>{$errors['username']}</span>";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="">
                                Password
                            </label>
                            <input type="password" class="form-control" name="password"
                                   autocomplete="off">
                            <?= makeCsrfFormField() ?>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-5">

                </div>

            </div>
        </div>
    </div>


<?php require_once 'footer.template.php';