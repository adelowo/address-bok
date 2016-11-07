<?php

require_once 'base.template.php';

$session = \SeedStars\Session::getInstance();

$contacts = $session->get("contacts");

$success = $session->get("success");
$classicError = $session->get("classic_error");

$session->remove("success");
$session->remove("classic_error");
$session->remove("contacts");
?>

    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-md-12">

                    <div class="col-1">


                    </div>

                    <div class="col-md-9">
                        <?php

                        if($classicError){
                            echo '<div class="alert alert-info">
                            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            	<strong>Whoops</strong> An error occured. Please try deleting again
                            </div>';
                        }

                        if ($success) {
                            echo '<div class="alert alert-success">
                            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            	<strong>Success</strong> The contact has been deleted
                            </div>';
                        }

                        ?>

                        <div class="table-responsive">
                            <p class="lead">Here is a list of all contacts in your address book</p>

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Email address</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php

                                $serialNumber = 0;

                                foreach ($contacts as $contact) {

                                    $serialNumber++;

                                    echo "<tr>";

                                    echo "<td>{$serialNumber}</td>";

                                    echo "<td>{$contact['fullname']}</td>";

                                    echo "<td>{$contact['email']}</td>";

                                    echo "
<td>
<form method='post' action='/delete'>" . makeCsrfFormField() . " 
<input type='submit' class='btn btn-danger' value='Delete' name='delete'> 
<input name='index' type='hidden' value='" . $serialNumber . "'>
</form>
</td>";

                                    echo "</tr>";
                                }

                                ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="col-2">

                    </div>

                </div>
            </div>
        </div>
    </div>


<?php require_once 'footer.template.php';