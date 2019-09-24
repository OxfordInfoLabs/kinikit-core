<?php

namespace Kinikit\Core\Validation;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Validation\FieldValidators\RegexpFieldValidator;

include_once "autoloader.php";

/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:29
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var Validator
     */
    private $validator;

    public function setUp(): void {
        $this->validator = Container::instance()->get(Validator::class);
    }

    public function testCanValidateObjectWithMarkupBasedValidation() {

        $validatedObject = new TestValidatedObject();

        $validationErrors = $this->validator->validateObject($validatedObject);

        $this->assertEquals(3, sizeof($validationErrors));

        $idErrors = $validationErrors["id"];

        $this->assertEquals(1, sizeof($idErrors));
        $this->assertEquals(new FieldValidationError("id", "required", "This field is required"), $idErrors["required"]);

        $usernameErrors = $validationErrors["username"];
        $this->assertEquals(1, sizeof($usernameErrors));
        $this->assertEquals(new FieldValidationError("username", "required", "This field is required"), $usernameErrors["required"]);

        $nameErrors = $validationErrors["name"];
        $this->assertEquals(1, sizeof($nameErrors));
        $this->assertEquals(new FieldValidationError("name", "required", "This field is required"), $nameErrors["required"]);


        $validatedObject->setId("marky");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $idErrors = $validationErrors["id"];
        $this->assertEquals(1, sizeof($idErrors));
        $this->assertEquals(new FieldValidationError("id", "numeric", "Value must be numeric"), $idErrors["numeric"]);

        $validatedObject->setUsername("__");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $usernameErrors = $validationErrors["username"];
        $this->assertEquals(2, sizeof($usernameErrors));
        $this->assertEquals(new FieldValidationError("username", "alphanumeric", "Value must be alphanumeric"), $usernameErrors["alphanumeric"]);
        $this->assertEquals(new FieldValidationError("username", "minLength", "Value must be at least 3 characters"), $usernameErrors["minLength"]);


        $validatedObject->setName("**Bang123**");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $nameErrors = $validationErrors["name"];
        $this->assertEquals(1, sizeof($nameErrors));
        $this->assertEquals(new FieldValidationError("name", "name", "Value must be a valid name"), $nameErrors["name"]);

        $validatedObject->setPassword("%%");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "minLength", "Value must be at least 8 characters"), $passwordErrors["minLength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);


        $validatedObject->setPassword("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "maxLength", "Value must be no greater than 16 characters"), $passwordErrors["maxLength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);

        $validatedObject->setAge(10);
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);

        $validatedObject->setAge(70);
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);


        $validatedObject->setShoeSize(2);
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "min", "Value must be at least 3"), $shoeSizeErrors["min"]);


        $validatedObject->setShoeSize(12);
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "max", "Value must be no greater than 11"), $shoeSizeErrors["max"]);

        $validatedObject->setEmailAddress("pinky");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(8, sizeof($validationErrors));
        $emailAddressErrors = $validationErrors["emailAddress"];
        $this->assertEquals(1, sizeof($emailAddressErrors));
        $this->assertEquals(new FieldValidationError("emailAddress", "email", "Value must be a valid email"), $emailAddressErrors["email"]);

        $validatedObject->setStandardDate("rrr");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(9, sizeof($validationErrors));
        $dateErrors = $validationErrors["standardDate"];
        $this->assertEquals(1, sizeof($dateErrors));
        $this->assertEquals(new FieldValidationError("standardDate", "date", "Value must be a date in d/m/Y format"), $dateErrors["date"]);

        $validatedObject->setCustomDate("rrr");
        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(10, sizeof($validationErrors));
        $dateErrors = $validationErrors["customDate"];
        $this->assertEquals(1, sizeof($dateErrors));
        $this->assertEquals(new FieldValidationError("customDate", "date", "Value must be a date in d-m-Y format"), $dateErrors["date"]);


        // Now clear down the validation queue
        $validatedObject->setId(44);
        $validatedObject->setUsername("mark123");
        $validatedObject->setName("Mark O'Reilly-Smythe");
        $validatedObject->setPassword("55ttaabb");
        $validatedObject->setConfirmPassword("55ttaabb");
        $validatedObject->setAge(18);
        $validatedObject->setShoeSize(11);
        $validatedObject->setEmailAddress("mark@oxil.gmail");
        $validatedObject->setStandardDate("06/12/1977");
        $validatedObject->setCustomDate("01-01-2017");

        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(0, sizeof($validationErrors));
    }

    public function testCustomValidationsMayBeUsedIfAddedAtRuntime() {

        $this->validator->addValidator("macaroni", new RegexpFieldValidator("[0-9][A-Z]", "This field is not Macaroni"));


        $customObject = new TestCustomValidatedObject();

        $validationErrors = $this->validator->validateObject($customObject);

        $this->assertEquals(1, sizeof($validationErrors));

        $customErrors = $validationErrors["customField"];
        $this->assertEquals(1, sizeof($customErrors));
        $this->assertEquals(new FieldValidationError("customField", "required", "This field is required"), $customErrors["required"]);

        $customObject->setCustomField("MM");

        $validationErrors = $this->validator->validateObject($customObject);

        $this->assertEquals(1, sizeof($validationErrors));

        $customErrors = $validationErrors["customField"];
        $this->assertEquals(1, sizeof($customErrors));
        $this->assertEquals(new FieldValidationError("customField", "macaroni", "This field is not Macaroni"), $customErrors["macaroni"]);


        $customObject->setCustomField("1M");
        $validationErrors = $this->validator->validateObject($customObject);
        $this->assertEquals(0, sizeof($validationErrors));

    }

}
