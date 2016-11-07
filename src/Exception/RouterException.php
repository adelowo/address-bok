<?php


namespace SeedStars\Exception;

use Exception;

class RouterException extends Exception
{

    const INVALID_ROUTE = "This route does not exist";

    const INVALID_REQUEST_TYPE = "Invalid HTTP Request method";
}
