<?php

require_once 'base.template.php';

$addedNewContact = session("success");
$error = session("errors");
$classicErrors = session("classic_error");
$duplicate = session("duplicated");

$session = \SeedStars\Session::getInstance();
$session->remove("success");
$session->remove("duplicated");

clearErrorsFromSession();
?>

    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <div class="col-md-3">

                </div>

                <div class="col-md-5">

                    <?php
                    if ($addedNewContact) {
                        echo '<div class="alert alert-success">
                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        	<strong>Success</strong> You added a new contact
                        </div>';
                    }

                    if ($classicErrors) {
                        echo '<div class="alert alert-danger">
                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        	<strong>Whoops</strong> Something went wrong and we are sorry. Please try again
                        </div>';
                    }

                    if ($duplicate) {
                        echo '<div class="alert alert-warning">
                        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        	<strong>Duplicated data</strong> 
                        	This value ('.$duplicate['value'].') already exists in your address book
                        </div>';
                    }
                    ?>

                    <form method="POST" role="form">
                        <legend>Add a new contact</legend>

                        <div class="form-group">
                            <label for="">Fullname : </label>
                            <input type="text" class="form-control" name="fullname"
                                   placeholder="John Doe">
                            <?php
                            if ($error['fullname']) {
                                echo '<span class="error">'.$error['fullname'].'</span>';
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="">Email address : </label>
                            <input type="email" class="form-control" name="mail"
                                   placeholder="john.doe@<?= $_SERVER['HTTP_HOST'] ?>">

                            <?php
                            if ($error['mail']) {
                                echo '<span class="error">'.$error['mail'].'</span>';
                            }
                            ?>

                        </div>
                        <?= makeCsrfFormField() ?>
                        <button type="submit" class="btn btn-primary">Add Entry</button>
                    </form>

                </div>

                <div class="col-md-3">

                </div>

            </div>
        </div>
    </div>


<?php require_once 'footer.template.php';