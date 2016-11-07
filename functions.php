<?php

function getCsrfToken()
{
    $session = \SeedStars\Session::getInstance();

    if ($session->has("_token")) {
        return $session->get("_token");
    }

    throwCsrfException(\SeedStars\Exception\CsrfException::CSRF_TOKEN_NOT_PRESENT);
}

function makeCsrfFormField()
{
    return "<input name='_token' type='hidden' value='" . getCsrfToken() . "'/>";
}

function makeCsrfToken()
{
    return bin2hex(random_bytes(10)); //could have resorted to `hash()` but i don't really like the idea of having to generate random strings from "abcde...1234.." all the time
}

function throwCsrfException(string $message)
{
    throw new \SeedStars\Exception\CsrfException($message);
}

/**
 * @param string $key
 * @return bool|mixed
 */
function session(string $key)
{
    return \SeedStars\Session::getInstance()->get($key);
}


/**
 * PDO is great but YAGNI. Just use sqlite here.
 * @return null|\SQLite3
 */
function getSqlite()
{
    static $connection = null;

    if (null === $connection) {
        $connection = new SQLite3("data/addressbook.sqlite");
    }

    return $connection;
}

/**
 * @return null|\PDO
 */
function getPDO()
{
    static $connection = null;

    if (null === $connection) {
        $connection = new PDO("sqlite:data/addressbook.sqlite", null, null);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $connection;
}


/**
 * @param array  $validatorTypes
 * @param object $errorBagHolder
 * @return object $errorBagHolder Holds an array of error bags
 * @throws \Exception
 */
function validator($validatorTypes, $errorBagHolder)
{

    $validationRules = [];

    foreach ($validatorTypes as $validator) {
        $validationRules[] = parseValidatorRules($validator);
    }

    foreach ($validationRules as $aRule) {

        $ruleName = $aRule['rule']['name'];

        switch ($ruleName) {

            case "length" :
                validateLengthRule($aRule, $errorBagHolder);
                continue;

            case "email" :
                validateEmailRule($aRule, $errorBagHolder);
                continue;

            default :
                throw new Exception(
                    "Unsupported Rule Type"
                );
        }
    }

    return $errorBagHolder;
}

function parseValidatorRules(string $index)
{
    $parsedRule = explode(":", $index);

    $index = $parsedRule[0];

    $ruleOptions = explode("=>", $parsedRule[1]);

    if (count($ruleOptions) === 1) {
        //for stuffs like the email rule where you do not have to specify "index:email=>blah" but "index:email" unlike the length rule where you can do "index:length=>2|100"
        //if it doesn't have extra addons, force the truthiness of the rule
        $ruleOptions[1] = true;
    }

    return [
        "index" => $index,
        "rule" => [
            "name" => $ruleOptions[0],
            "params" => $ruleOptions[1]
        ]
    ];
}

/**
 * @param array  $aRule
 * @param object $errorBagHolder
 */
function validateLengthRule(array $aRule, $errorBagHolder)
{
    //index 0 would hold the minimum value while 1 is the max value
    $minAndMax = explode("|", $aRule['rule']['params']);

    $errorBag = validationErrorBag($aRule['index']);

    if (mb_strlen($_POST[$aRule['index']], "UTF-8") < $minAndMax[0]) {
        $errorBag->add([
            "index" => $aRule['index'],
            "message" => "The {$aRule['index']} field should be not lesser than {$minAndMax[0]}"
        ]);
    }

    //only check for max length if it is not set to null.
    //Null here should be a string
    if ("null" !== $minAndMax[1]) {
        if (mb_strlen($_POST[$aRule['index']], "UTF-8") > $minAndMax[1]) {
            $errorBag->add([
                "index" => $aRule['index'],
                "message" => "The {$aRule['index']} field should not be greater than {$minAndMax[1]}"
            ]);
        }
    }

    if (count($errorBag->get())) {
        $errorBagHolder->addBag($errorBag);
    }
}

function validateEmailRule(array $aRule, $errorBagHolder)
{
    $index = $aRule['index'];

    $errorBag = validationErrorBag($index);

    if (!filter_var($_POST[$index], FILTER_VALIDATE_EMAIL)) {
        $errorBag->add([
            "index" => $index,
            "message" => "This doesn't seem like a valid email address"
        ]);

        $errorBagHolder->addBag($errorBag);
    }
}

function errorBagHolder()
{
    return new class implements Countable
    {
        protected $bags = [];

        public function addBag($bag)
        {
            $this->bags[$bag->getName()] = $bag;
        }

        public function getBag(string $bagName)
        {
            return $this->bags[$bagName];
        }

        public function count()
        {
            return count($this->bags);
        }

        public function getAll()
        {
            $all = [];

            foreach ($this->bags as $aBag) {

                $bagName = $aBag->getName();
                $all[$bagName] = $aBag->get()[$bagName]['message'];
            }

            return $all;
        }
    };
}

function validationErrorBag(string $bagName)
{
    return new class($bagName)
    {

        protected $name;

        protected $errors = [];

        public function __construct(string $bagName)
        {
            $this->name = $bagName;
        }

        public function add(array $errors)
        {
            $this->errors[$errors['index']] = $errors;
        }

        public function get()
        {
            return $this->errors;
        }

        public function getName()
        {
            return $this->name;
        }
    };
}

function sanitize(string $value ,string $type = "string")
{
    if ("email" === $type) {
        return sanitizeEmail($value);
    }

    return filter_var($value, FILTER_SANITIZE_STRING);
}

/**
 * @param string $email
 * @return mixed
 */
function sanitizeEmail(string $email)
{
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

function clearErrorsFromSession()
{
    return \SeedStars\Session::getInstance()->remove("errors");
}

function failedLoginResponse(\SeedStars\Session $session, string $location)
{
    $session->put("failed", true);
    header("Location: {$location}");
    exit();
}