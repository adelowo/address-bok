<?php

use SeedStars\Exception\InvalidHttpVerbException;
use SeedStars\Exception\RouterException;

function initRouter(array $routes)
{
    $matchedRoute = $_SERVER['REQUEST_URI'];

    if (!array_key_exists($matchedRoute, $routes)) {
        throwRouterException(RouterException::INVALID_ROUTE);
    }

    return parseRouteInformation($routes[$matchedRoute]);
}

function parseRouteInformation(array $matchedRoute)
{
    $httpVerbs = explode("|", $matchedRoute['verb']);

    if (!in_array(trim($_SERVER['REQUEST_METHOD']), $httpVerbs)) {

        throwRouterException(
            RouterException::INVALID_REQUEST_TYPE . ". The supported verbs for this routes are {$httpVerbs[0]} and {$httpVerbs[1]}"
        );
    }

    $session = startSession();

    //Only update the csrf token on a HTTP request other than POST.
    //This is so, as we can easily compare.
    //If it isn't done like this, we inherently overwrite the token used to serve the request (since this is a fresh request and the token is "re-cooked" on every request initialization).
    //Hence an Exception would always be thrown
    if ("POST" !== $_SERVER['REQUEST_METHOD']) {
        $session->put("_token", makeCsrfToken());
    }

    return call_user_func_array(
        $matchedRoute['handler'],
        [
            $session
            ,
            $_SERVER['REQUEST_METHOD']
        ]
    );
}

function startSession()
{
    return \SeedStars\Session::getInstance()->start();
}

function throwRouterException(string $message)
{
    throw new RouterException($message);
}

function throwInvalidRequestException(string $invalidRequestMethod)
{
    throw new InvalidHttpVerbException($invalidRequestMethod);
}


function getAbsoluteUriForRoute(string $route)
{
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    $uri = ($uri) ? $uri : '';
    $route = ($route) ? $route : '';

    return "http://{$host}{$uri}/{$route}";
}

/**
 * Some sort of middleware for routes that processes a POST HTTP request.
 * @throws \Exception if the csrf token does not exists in the session
 */
function preEnterPostedRoute()
{
    if (strcmp(getCsrfToken(), $_POST['_token']) === 0) {
        return;
    }

    throwCsrfException(
        \SeedStars\Exception\CsrfException::CSRF_TOKEN_NOT_PRESENT
    );
}

/**
 * middleware for authenticated resource access
 */
function preAuthContentAccess()
{
    if (!session(LOGGED_IN_USER)) {
        $to = getAbsoluteUriForRoute("login");
        header("Location: {$to}");
        exit();
    }
}