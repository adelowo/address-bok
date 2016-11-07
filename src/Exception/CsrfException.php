<?php

namespace SeedStars\Exception;

use Exception;

class CsrfException extends Exception
{

    const CSRF_TOKEN_NOT_PRESENT = "This seems more of a fraudulent activity";


}