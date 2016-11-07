<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="public/app.css">
    <title>Seedstar Address Book</title>

</head>
<body>

<header>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <a class="navbar-brand" href="#"><?= APP_NAME ?></a>
        <ul class="nav navbar-nav">
            <li>
                <a href="#">Home</a>
            </li>
            <li>
                <?php
                $link = '';
                if (!session(LOGGED_IN_USER)) {

                    if ("signup" === $_SERVER['REQUEST_URI']) {
                        $link = "<a href='/signup'>Register</a>";
                    } else {
                        $link = "<a href='/login'>Login</a>";
                    }

                } else {
                    $link = "<a href='/create'>Add New</a>";
                }
                echo $link;
                ?>
            </li>
        </ul>
    </nav>
</header>

<main>
