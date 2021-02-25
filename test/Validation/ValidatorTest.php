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
        $this->assertEquals(new FieldValidationError("standardDate", "date", "Value must be a date in Y-m-d format"), $dateErrors["date"]);

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
        $validatedObject->setStandardDate("1977-12-06");
        $validatedObject->setCustomDate("01-01-2017");

        $validationErrors = $this->validator->validateObject($validatedObject);
        $this->assertEquals(0, sizeof($validationErrors));
    }

    public function testCustomValidationsMayBeUsedIfAddedAtRuntime() {

        $this->validator->addValidator(new RegexpFieldValidator("[0-9][A-Z]", "macaroni", "This field is not Macaroni"));


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


    public function testValidateMethodIsCalledOnObjectIfDefined() {

        $object = new TestValidationMethodObject();

        // Validate the object with custom method.
        $errors = $this->validator->validateObject($object);

        $this->assertEquals(2, sizeof($errors));
        $this->assertTrue(isset($errors["name"]["required"]));
        $this->assertTrue(isset($errors["custom"]));

    }


    public function testCanValidateArrayUsingValidationDefinition() {


        $validationDefinition = json_decode(file_get_contents(__DIR__ . "/validation-definition.json"), true);
        $validatedArray = [];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);


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


        $validatedArray["id"] = "marky";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(3, sizeof($validationErrors));
        $idErrors = $validationErrors["id"];
        $this->assertEquals(1, sizeof($idErrors));
        $this->assertEquals(new FieldValidationError("id", "numeric", "Value must be numeric"), $idErrors["numeric"]);

        $validatedArray["username"] = "__";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(3, sizeof($validationErrors));
        $usernameErrors = $validationErrors["username"];
        $this->assertEquals(2, sizeof($usernameErrors));
        $this->assertEquals(new FieldValidationError("username", "alphanumeric", "Value must be alphanumeric"), $usernameErrors["alphanumeric"]);
        $this->assertEquals(new FieldValidationError("username", "minLength", "Value must be at least 3 characters"), $usernameErrors["minLength"]);


        $validatedArray["name"] = "**Bang123**";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(3, sizeof($validationErrors));
        $nameErrors = $validationErrors["name"];
        $this->assertEquals(1, sizeof($nameErrors));
        $this->assertEquals(new FieldValidationError("name", "name", "Value must be a valid name"), $nameErrors["name"]);

        $validatedArray["password"] = "%%";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "minLength", "Value must be at least 8 characters"), $passwordErrors["minLength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);


        $validatedArray["password"] = "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(5, sizeof($validationErrors));
        $passwordErrors = $validationErrors["password"];
        $this->assertEquals(2, sizeof($passwordErrors));
        $this->assertEquals(new FieldValidationError("password", "regexp", "Value does not match the required format"), $passwordErrors["regexp"]);
        $this->assertEquals(new FieldValidationError("password", "maxLength", "Value must be no greater than 16 characters"), $passwordErrors["maxLength"]);
        $confirmPasswordErrors = $validationErrors["confirmPassword"];
        $this->assertEquals(1, sizeof($confirmPasswordErrors));
        $this->assertEquals(new FieldValidationError("confirmPassword", "equals", "Value does not match the password field"), $confirmPasswordErrors["equals"]);

        $validatedArray["age"] = 10;
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);

        $validatedArray["age"] = 70;
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(6, sizeof($validationErrors));
        $ageErrors = $validationErrors["age"];
        $this->assertEquals(1, sizeof($ageErrors));
        $this->assertEquals(new FieldValidationError("age", "range", "Value must be between 18 and 65"), $ageErrors["range"]);


        $validatedArray["shoeSize"] = 2;
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "min", "Value must be at least 3"), $shoeSizeErrors["min"]);


        $validatedArray["shoeSize"] = 12;
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(7, sizeof($validationErrors));
        $shoeSizeErrors = $validationErrors["shoeSize"];
        $this->assertEquals(1, sizeof($shoeSizeErrors));
        $this->assertEquals(new FieldValidationError("shoeSize", "max", "Value must be no greater than 11"), $shoeSizeErrors["max"]);

        $validatedArray["emailAddress"] = "pinky";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(8, sizeof($validationErrors));
        $emailAddressErrors = $validationErrors["emailAddress"];
        $this->assertEquals(1, sizeof($emailAddressErrors));
        $this->assertEquals(new FieldValidationError("emailAddress", "email", "Value must be a valid email"), $emailAddressErrors["email"]);

        $validatedArray["standardDate"] = "RRR";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(9, sizeof($validationErrors));
        $dateErrors = $validationErrors["standardDate"];
        $this->assertEquals(1, sizeof($dateErrors));
        $this->assertEquals(new FieldValidationError("standardDate", "date", "Value must be a date in Y-m-d format"), $dateErrors["date"]);

        $validatedArray["customDate"] = "RRR";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(10, sizeof($validationErrors));
        $dateErrors = $validationErrors["customDate"];
        $this->assertEquals(1, sizeof($dateErrors));
        $this->assertEquals(new FieldValidationError("customDate", "date", "Value must be a date in d-m-Y format"), $dateErrors["date"]);


        $validatedArray["pickOne"] = "Ginger";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(11, sizeof($validationErrors));
        $pickOneErrors = $validationErrors["pickOne"];
        $this->assertEquals(1, sizeof($pickOneErrors));
        $this->assertEquals(new FieldValidationError("pickOne", "values", "Value must be one of [Green, Blue, Silk]"), $pickOneErrors["values"]);

        $validatedArray["pickOneStructured"] = "ginger";
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(12, sizeof($validationErrors));
        $pickOneStructuredErrors = $validationErrors["pickOneStructured"];
        $this->assertEquals(1, sizeof($pickOneStructuredErrors));
        $this->assertEquals(new FieldValidationError("pickOneStructured", "values", "Value must be one of [green, blue, silk]"), $pickOneStructuredErrors["values"]);


        $validatedArray["pickMany"] = ["Ginger", "Spice"];
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(14, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("pickMany:0", "values", "Value must be one of [Green, Blue, Silk]"), $validationErrors["pickMany:0"]["values"]);
        $this->assertEquals(new FieldValidationError("pickMany:1", "values", "Value must be one of [Green, Blue, Silk]"), $validationErrors["pickMany:1"]["values"]);


        $validatedArray["pickManyStructured"] = ["Ginger", "Spice"];
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(16, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("pickManyStructured:0", "values", "Value must be one of [green, blue, silk]"), $validationErrors["pickManyStructured:0"]["values"]);
        $this->assertEquals(new FieldValidationError("pickManyStructured:1", "values", "Value must be one of [green, blue, silk]"), $validationErrors["pickManyStructured:1"]["values"]);


        // Now clear down the validation queue
        $validatedArray["id"] = 44;
        $validatedArray["username"] = "mark123";
        $validatedArray["name"] = "Mark O'Reilly-Smythe";
        $validatedArray["password"] = "55ttaabb";
        $validatedArray["confirmPassword"] = "55ttaabb";
        $validatedArray["age"] = 18;
        $validatedArray["shoeSize"] = 11;
        $validatedArray["emailAddress"] = "mark@oxil.gmail";
        $validatedArray["standardDate"] = "1977-12-06";
        $validatedArray["customDate"] = "01-01-2017";
        $validatedArray["pickOne"] = "Green";
        $validatedArray["pickOneStructured"] = "blue";
        $validatedArray["pickMany"] = ["Silk"];
        $validatedArray["pickManyStructured"] = ["green"];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(0, sizeof($validationErrors));


        // Check validation for multiple item
        $validatedArray["indexes"] = 44;
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(1, sizeof($validationErrors));
        $indexesErrors = $validationErrors["indexes"];
        $this->assertEquals(1, sizeof($indexesErrors));
        $this->assertEquals(new FieldValidationError("indexes", "multiple", "Value must be an array"), $indexesErrors["multiple"]);


        // Associative array should fail
        $validatedArray["indexes"] = [
            "item1" => "Mark",
            "item2" => "John"
        ];
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(1, sizeof($validationErrors));
        $indexesErrors = $validationErrors["indexes"];
        $this->assertEquals(1, sizeof($indexesErrors));
        $this->assertEquals(new FieldValidationError("indexes", "multiple", "Value must be an array"), $indexesErrors["multiple"]);


        $validatedArray["indexes"] = [
            "Hello",
            "Dolly"
        ];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(2, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("indexes:0", "numeric", "Value must be numeric"), $validationErrors["indexes:0"]["numeric"]);
        $this->assertEquals(new FieldValidationError("indexes:1", "numeric", "Value must be numeric"), $validationErrors["indexes:1"]["numeric"]);


        $validatedArray["indexes"] = [
            0,
            5
        ];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(0, sizeof($validationErrors));


        // Check validation for recursive inline item.
        $validatedArray["subItems"] = [
            "title" => "Bonzo"
        ];

        // Should be an array of objects
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(1, sizeof($validationErrors));
        $subItemErrors = $validationErrors["subItems"];
        $this->assertEquals(1, sizeof($subItemErrors));
        $this->assertEquals(new FieldValidationError("subItems", "multiple", "Value must be an array"), $subItemErrors["multiple"]);


        // Missing required fields
        $validatedArray["subItems"] = [
            [],
            []
        ];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(2, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("subItems:0:title", "required", "This field is required"), $validationErrors["subItems:0:title"]["required"]);
        $this->assertEquals(new FieldValidationError("subItems:1:title", "required", "This field is required"), $validationErrors["subItems:1:title"]["required"]);


        // Fix required fields
        $validatedArray["subItems"] = [
            ["title" => "Jones"],
            ["title" => "Brown"]
        ];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(0, sizeof($validationErrors));


        $validatedArray["subItems"] = [
            [
                "title" => "Jones",
                "subItems" => [
                    [],
                    []
                ]
            ]
        ];

        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(2, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("subItems:0:subItems:0:title", "required", "This field is required"), $validationErrors["subItems:0:subItems:0:title"]["required"]);
        $this->assertEquals(new FieldValidationError("subItems:0:subItems:1:title", "required", "This field is required"), $validationErrors["subItems:0:subItems:1:title"]["required"]);


        $validatedArray["subItems"] = [
            [
                "title" => "Jones",
                "subItems" => [
                    ["title" => "Smith",
                        "subItems" => [
                            []
                        ]
                    ],
                    ["title" => "Davis"]
                ]
            ]
        ];
        $validationErrors = $this->validator->validateArray($validatedArray, $validationDefinition);
        $this->assertEquals(1, sizeof($validationErrors));
        $this->assertEquals(new FieldValidationError("subItems:0:subItems:0:subItems:0:title", "required", "This field is required"), $validationErrors["subItems:0:subItems:0:subItems:0:title"]["required"]);


    }


}
