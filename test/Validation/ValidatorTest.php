<?php

namespace Kinikit\Core\Validation;

include_once "autoloader.php";

/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:29
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase {


    public function testCanValidateObjectWithMarkupBasedValidation() {

        $validatedObject = new TestValidatedObject();

        $validationErrors = Validator::instance()->validateObject($validatedObject);

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
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $idErrors = $validationErrors["id"];
        $this->assertEquals(1, sizeof($idErrors));
        $this->assertEquals(new FieldValidationError("id", "numeric", "Value must be numeric"), $idErrors["numeric"]);

        $validatedObject->setUsername("__");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $usernameErrors = $validationErrors["username"];
        $this->assertEquals(2, sizeof($usernameErrors));
        $this->assertEquals(new FieldValidationError("username", "alphanumeric", "Value must be alphanumeric"), $usernameErrors["alphanumeric"]);
        $this->assertEquals(new FieldValidationError("username", "minlength", "Value must be at least 3 characters"), $usernameErrors["minlength"]);


        $validatedObject->setName("**Bang123**");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(3, sizeof($validationErrors));
        $nameErrors = $validationErrors["name"];
        $this->assertEquals(1, sizeof($nameErrors));
        $this->assertEquals(new FieldValidationError("name", "name", "Value must be a valid name"), $nameErrors["name"]);

        $validatedObject->setPassword("%%");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "minlength", "Value must be at least 8 characters"), $passwordErrors["minlength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);


        $validatedObject->setPassword("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "maxlength", "Value must be no greater than 16 characters"), $passwordErrors["maxlength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);

        $validatedObject->setAge(10);
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);

        $validatedObject->setAge(70);
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);


        $validatedObject->setShoeSize(2);
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "min", "Value must be at least 3"), $shoeSizeErrors["min"]);


        $validatedObject->setShoeSize(12);
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "max", "Value must be no greater than 11"), $shoeSizeErrors["max"]);

        $validatedObject->setEmailAddress("pinky");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(8, sizeof($validationErrors));
        $emailAddressErrors = $validationErrors["emailAddress"];
        $this->assertEquals(1, sizeof($emailAddressErrors));
        $this->assertEquals(new FieldValidationError("emailAddress", "email", "Value must be a valid email"), $emailAddressErrors["email"]);

        $validatedObject->setStandardDate("rrr");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(9, sizeof($validationErrors));
        $dateErrors = $validationErrors["standardDate"];
        $this->assertEquals(1, sizeof($dateErrors));
        $this->assertEquals(new FieldValidationError("standardDate", "date", "Value must be a date in d/m/Y format"), $dateErrors["date"]);

        $validatedObject->setCustomDate("rrr");
        $validationErrors = Validator::instance()->validateObject($validatedObject);
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

        $validationErrors = Validator::instance()->validateObject($validatedObject);
        $this->assertEquals(0, sizeof($validationErrors));
    }

    public function testCustomValidationsAreLoadedFromConfigFile() {

        $customObject = new TestCustomValidatedObject();

        $validationErrors = Validator::instance()->validateObject($customObject);

        $this->assertEquals(1, sizeof($validationErrors));

        $customErrors = $validationErrors["customField"];
        $this->assertEquals(1, sizeof($customErrors));
        $this->assertEquals(new FieldValidationError("customField", "required", "This field is required"), $customErrors["required"]);

        $customObject->setCustomField("MM");

        $validationErrors = Validator::instance()->validateObject($customObject);

        $this->assertEquals(1, sizeof($validationErrors));

        $customErrors = $validationErrors["customField"];
        $this->assertEquals(1, sizeof($customErrors));
        $this->assertEquals(new FieldValidationError("customField", "macaroni", "This field is not Macaroni"), $customErrors["macaroni"]);


        $customObject->setCustomField("1M");
        $validationErrors = Validator::instance()->validateObject($customObject);
        $this->assertEquals(0, sizeof($validationErrors));

    }

}