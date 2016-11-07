<?php

ob_start();

require_once "vendor/autoload.php";

define("LOGGED_IN_USER", "user");
define("APP_NAME", "Seedstars");

function listAllEntries(\SeedStars\Session $session, string $httpVerb = "GET")
{
    if ("GET" !== $httpVerb) {
        throwInvalidRequestException($httpVerb);
    }

    if (!$session->has(LOGGED_IN_USER)) {
        $to = getAbsoluteUriForRoute("login");
        header("Location: {$to}");
        exit();
    }

    return makeView("index");
}

function addNewEntryToAddressBook(\SeedStars\Session $session, string $httpVerb = "GET")
{
    if (!in_array($httpVerb, ["GET", "POST"])) {
        throwInvalidRequestException(
            \SeedStars\Exception\InvalidHttpVerbException::UNSUPPORTED_HTTP_REQUEST_METHOD
        );
    }

    preAuthContentAccess();

    if ("GET" === $httpVerb) {
        return makeView("create");
    }

    preEnterPostedRoute();

    try {

        $rules = [
            "fullname:length=>3|50",
            "mail:email"
        ];

        $errorBagHolder = errorBagHolder();

        validator($rules, $errorBagHolder);

        if (0 !== $errorBagHolder->count()) {

            $session->put("errors", $errorBagHolder->getAll());
            $to = getAbsoluteUriForRoute("add");
            header("Location: {$to}");
            exit();
        }

        $pdo = getPDO();

        $checkDuplicateEmail = $pdo->prepare("SELECT email FROM address_book WHERE email = :email");
        $checkDuplicateEmail->bindParam(":email", sanitize($_POST['mail'], "email"));

        if (0 !== $checkDuplicateEmail->rowCount()) {

            $session->put("duplicated", ["status" => true, "value" => strip_tags(sanitize($_POST['mail']))]);

            $to = getAbsoluteUriForRoute("add");
            header("Location: {$to}");
            exit();
        }

        $statement = $pdo->prepare("INSERT INTO address_book(fullname, email) VALUES (:fullname, :email)");
        $statement->bindValue(":fullname", sanitize($_POST['fullname']));
        $statement->bindValue(":email", sanitize($_POST['mail'], "email"));

        $to = getAbsoluteUriForRoute("add");

        if ($statement->execute()) {
            $session->put("success", true);
            header("Location: {$to}");
            exit();
        }

        $session->put("classic_error", "Something went wrong");

        header("Location: {$to}");
        exit();


    } catch (Throwable $e) {

        echo $e->getMessage();
        var_dump($e->getTrace());
    }
}

function logUserIntoApplication(\SeedStars\Session $session, string $httpVerb)
{
    if (!in_array($httpVerb, ["GET", "POST"])) {
        throwInvalidRequestException(
            \SeedStars\Exception\InvalidHttpVerbException::UNSUPPORTED_HTTP_REQUEST_METHOD
        );
    }

    if ($session->has(LOGGED_IN_USER)) {
        $to = getAbsoluteUriForRoute("/");
        header("Location: {$to}");
        exit();
    }

    if ("GET" === $httpVerb) {
        return makeView("login");
    }

}

function validateCredentials()
{

}

function logOut()
{

}

function registerUser(\SeedStars\Session $session, string $httpVerb)
{
    if (!in_array($httpVerb, ["GET", "POST"])) {
        throwInvalidRequestException(
            \SeedStars\Exception\InvalidHttpVerbException::UNSUPPORTED_HTTP_REQUEST_METHOD
        );
    }

    //Only Non-logged users can ever get to this route
    if ($session->has(LOGGED_IN_USER)) {
        $to = getAbsoluteUriForRoute("/");
        header("Location: {$to}");
        exit();
    }

    if ("GET" === $httpVerb) {
        return makeView("signup");
    }

    try {

        preEnterPostedRoute();

        $rules = [
            "fullname:length=>3|50",
            "username:length=>3|25",
            "mail:email",
            "password:length=>3|null"
        ];

        $errorBagHolder = errorBagHolder();

        validator($rules, $errorBagHolder);

        if (0 !== $errorBagHolder->count()) {

            //redirect back if there are errors.
            //add the error bag to the session.
            $session->put("errors", $errorBagHolder->getAll());

            $to = getAbsoluteUriForRoute("signup");
            header("Location: {$to}");
            exit();
        }


//        $sqliteHandler = getSqlite();

        $pdo = getPDO();

        $statement = $pdo->prepare(
            "INSERT INTO users(fullname, username, email_adress, password) VALUES (:fullname, :username, :email, :password)"
        );

        $statement->bindParam(":fullname", sanitize($_POST['fullname']));
        $statement->bindParam(":username", sanitize($_POST['username']));
        $statement->bindParam(":email", sanitize($_POST['mail'], "email"));
        $statement->bindParam(":password", password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT));

        if ($statement->execute()) {

            $session->put(LOGGED_IN_USER, true);

            $to = getAbsoluteUriForRoute("/");
            header("Location: {$to}");
            exit();
        }

        $session->put("classic_error", "Something went wron with that request. Please try again");
        $to = getAbsoluteUriForRoute("signup");
        header("Location: {$to}");
        exit();

    } catch (Throwable $e) {

        echo $e->getMessage();
        var_dump($e->getTrace());
    }
}

$routes = [
    "/signup" => [
        "verb" => "GET|POST",
        "handler" => "registerUser"
    ],
    "/login" => [
        "verb" => "GET|POST",
        "handler" => 'logUserIntoApplication'
    ],
    "/logout" => [
        "verb" => "GET",
        "handler" => 'logOut'
    ],
    "/add" => [
        "verb" => "GET|POST",
        "handler" => 'addNewEntryToAddressBook'
    ],
    "delete" => [
        "verb" => "POST",
        "handler" => "deleteEntryFromAddressBook"
    ],
    "/" => [
        "verb" => "GET",
        "handler" => 'listAllEntries'
    ]
];

initRouter($routes);

ob_flush();
ob_end_flush();