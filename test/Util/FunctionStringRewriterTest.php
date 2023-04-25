<?php

namespace Kinikit\Core\Util;

use Kinikit\Core\Exception\InsufficientParametersException;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class FunctionStringRewriterTest extends TestCase {


    public function testCanExtractArgumentsFromFunctions() {

        $this->assertEquals([7, 10], FunctionStringRewriter::extractArgs("MAX(7,10)", "MAX"));
        $this->assertEquals([5, 6, 7, 8], FunctionStringRewriter::extractArgs("GREATEST(5,6,7,8)", "GREATEST"));
        $this->assertEquals(["CONCAT('A','B','C')"], FunctionStringRewriter::extractArgs("GROUP_CONCAT(CONCAT('A','B','C'))", "GROUP_CONCAT"));
        $this->assertEquals(["'A'", "'B'", "'C'"], FunctionStringRewriter::extractArgs("GROUP_CONCAT(CONCAT('A','B','C'))", "CONCAT", false));
        $this->assertEquals(["max(4,5,6)", "min(6,5,4)", "concat(min(6,5,4),max(5,8))"], FunctionStringRewriter::extractArgs("REPLACE(max(4,5,6),min(6,5,4),concat(min(6,5,4),max(5,8)))", "REPLACE"));

        $this->assertEquals([], FunctionStringRewriter::extractArgs("SOMEFUNCTION()", "OTHER"));


    }

    public function testCanRewriteSimpleFunction() {

        $sql = "MAX(5,7)";
        $result = FunctionStringRewriter::rewrite($sql, "MAX", "MIN($1,$2)", [null, null]);
        $this->assertEquals("MIN(5,7)", $result);

        $sql = "GROUP_CONCAT(CONCAT('A','B','C'))";
        $result = FunctionStringRewriter::rewrite($sql, "CONCAT", "MAX($1,$2,$3)", [1, 2, 3]);
        $this->assertEquals("GROUP_CONCAT(MAX('A','B','C'))", $result);

        $sql = "INSTR(FIRST,SECOND)";
        $result = FunctionStringRewriter::rewrite($sql, "INSTR", "POSITION($1 IN $2)", [null, null]);
        $this->assertEquals("POSITION(FIRST IN SECOND)", $result);

        $sql = "INSTR(GROUP_CONCAT(CONCAT('A','B','C'),';'),CONCAT('D','E','F'))";
        $result = FunctionStringRewriter::rewrite($sql, "INSTR", "POSITION($1 IN $2)", [null, null]);
        $this->assertEquals("POSITION(GROUP_CONCAT(CONCAT('A','B','C'),';') IN CONCAT('D','E','F'))", $result);
    }

    public function testCanReorderArguments() {

        $sql = "INSTR(one,two,three)";
        $result = FunctionStringRewriter::rewrite($sql, "INSTR", "INSTR($3,$1,$2)", [null, null, null]);
        $this->assertEquals("INSTR(three,one,two)", $result);
    }

    public function testDefaultValuesUsedIfNoArgumentFromOrigin() {

        $sql = "MAX(5,7)";
        $result = FunctionStringRewriter::rewrite($sql, "MAX", "MIN($1,$2,$3)", [1, 2, 3]);
        $this->assertEquals("MIN(5,7,3)", $result);

        $sql = "GROUP_CONCAT(CONCAT('A'))";
        $result = FunctionStringRewriter::rewrite($sql, "CONCAT", "MAX($1,$2,$3)", ["one", "two", "three"]);
        $this->assertEquals("GROUP_CONCAT(MAX('A',two,three))", $result);

        $sql = "GREATEST()";
        $result = FunctionStringRewriter::rewrite($sql, "GREATEST", "MAX($1,$2)", ["A", "B"]);
        $this->assertEquals("MAX(A,B)", $result);
    }

    public function testCanReplaceMultipleNestedInstancesOfFunction() {

        $sql = "CONCAT(CONCAT(X,Y),CONCAT(Z,A))";
        $result = FunctionStringRewriter::rewrite($sql, "CONCAT", "MAX($1,$2,$3)", ["one", "two", "three"]);
        $this->assertEquals("MAX(MAX(X,Y,three),MAX(Z,A,three),three)", $result);

    }

    public function testCanReplaceMultipleInstancesOfFunctionAtTopLevel() {

        $sql = "POW(COUNT(X), COUNT(Y))";
        $result = FunctionStringRewriter::rewrite($sql, "COUNT", "SUM($1)", [null]);
        $this->assertEquals("POW(SUM(X), SUM(Y))", $result);

        $sql = "DO INSTR(X,CONCAT(Z,Y)) WHERE CONCAT(A,B,C) IS NULL";
        $result = FunctionStringRewriter::rewrite($sql, "CONCAT", "MIN($1,$2)", [5, 6]);
        $this->assertEquals("DO INSTR(X,MIN(Z,Y)) WHERE MIN(A,B) IS NULL", $result);

    }

    public function testCanRewriteCorrectlyWhenParametersPresent() {

        $sql = "SELECT ?, COUNT(*) FROM test WHERE ? IS NOT NULL";
        $result = FunctionStringRewriter::rewrite($sql, "COUNT", "SUM($1)", [null]);
        $this->assertEquals("SELECT ?, SUM(*) FROM test WHERE ? IS NOT NULL", $result);

    }

    public function testRewriteWorksWhenSearchFunctionNotPresentInString() {

        $sql = "LEAST(POW(X,Y),Z,CONCAT(A,B,C))";
        $result = FunctionStringRewriter::rewrite($sql, "MAX", "GREATEST($1,$2)", [null, null]);
        $this->assertEquals($sql, $result);

    }

    public function testRewriteWorksOnEmptyParameters() {

        $sql = "";
        $result = FunctionStringRewriter::rewrite($sql, "MAX", "MIN($1)", [null]);
        $this->assertEquals($sql, $result);

        $sql = "test";
        $result = FunctionStringRewriter::rewrite($sql, "", "MAX($1)", [null]);
        $this->assertEquals($sql, $result);


    }

    public function testExceptionThrownWhenInsufficientDefaultValuesProvided() {

        try {
            $sql = "test()";
            $result = FunctionStringRewriter::rewrite($sql, "test", "function($1,$2)", [1]);
            $this->fail("Should have thrown here");

        } catch (InsufficientParametersException $e) {
            $this->assertEquals("Number of default values doesn't match.", $e->getMessage());
        }


    }

    public function testSpacesAreNotAnIssueWhenRewritingFunctions() {

        $sql1 = "function(this, that)";
        $sql2 = "function(one,two)";

        $result1 = FunctionStringRewriter::rewrite($sql1, "function", "function($2, $1)", [null, null]);
        $result2 = FunctionStringRewriter::rewrite($sql2, "function", "function($2, $1)", [null, null]);

        $this->assertEquals("function(that, this)", $result1);
        $this->assertEquals("function(two, one)", $result2);
    }

    public function testCanInsertParameterValuesWhenMultipleInstancesOfParameter() {

        $sql = "function(?)";
        $params = ["bing"];
        $result = FunctionStringRewriter::rewrite($sql, "function", "function($1, $1)", [null, null], $params);

        $this->assertEquals("function(?, ?)", $result);
        $this->assertEquals(["bing", "bing"], $params);
    }

    public function testCanReorderParameters() {

        $sql = "CONCAT(?, ?, ?)";
        $params = [1, 2, 3];
        $result = FunctionStringRewriter::rewrite($sql, "CONCAT", "CONCAT($3, $1, $2)", [null, null, null], $params);

        $this->assertEquals("CONCAT(?, ?, ?)", $result);
        $this->assertEquals([3, 1, 2], $params);

        $sql = "MAX(?, MIN(?, ?))";
        $params = ["one", "two", "three"];
        $result = FunctionStringRewriter::rewrite($sql, "MIN", "AVG($2, $1)", [null, null], $params);

        $this->assertEquals("MAX(?, AVG(?, ?))", $result);
        $this->assertEquals(["one", "three", "two"], $params);

        $sql = "MAX(?,MIN(?,?))";
        $params = ["one", "two", "three"];
        $result = FunctionStringRewriter::rewrite($sql, "MAX", "AVG($2, $1)", [null, null], $params);

        $this->assertEquals("AVG(MIN(?,?), ?)", $result);
        $this->assertEquals(["two", "three", "one"], $params);
    }

}