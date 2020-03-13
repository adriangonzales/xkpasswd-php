<?php

namespace Adgodev\Xkpasswd;

use PHPUnit\Framework\TestCase;

class PasswordGeneratorTest extends TestCase
{
    public function testWithoutArguments()
    {
        $password = PasswordGenerator::generate();

        $passwordParts = count($this->splitIntoParts($password));

        $this->assertRegExp('/[aA-zZ]/', $password, 'Password does not contain alphanumeric characters');

        $this->assertEquals(
            $passwordParts,
            3,
            "Default password does not produce three parts"
        );
    }

    public function testWithDelimiter()
    {
        $password = PasswordGenerator::generate([
            'separators' => '.',
            'pattern' => 'wswswsw',
        ]);

        $this->assertRegExp('/((\w+)\.(\w+)\.(\w+)\.(\w+))/', $password);
    }

    public function testTransformOptionsUppercase()
    {
        $password = PasswordGenerator::generate([
            'transform' => 'uppercase'
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) {
            $this->assertRegExp('/\W+|[A-Z]+/', $part);
        });
    }

    public function testTransformOptionsLowercase()
    {
        $password = PasswordGenerator::generate([
            'transform' => 'lowercase'
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) {
            $this->assertRegExp('/\W+|[a-z]+/', $part);
        });
    }

    public function testTransformOptionsAlternateCase()
    {
        $password = PasswordGenerator::generate([
            'transform' => 'alternate'
        ]);

        $passwordParts = $this->splitIntoParts($password);
        $expectUppercase = true;

        array_walk($passwordParts, function ($part) use (&$expectUppercase) {
            if ($expectUppercase) {
                $this->assertRegExp('/^[A-Z]+$/', $part);
            } else {
                $this->assertRegExp('/^[a-z]+$/', $part);
            }
            $expectUppercase = !$expectUppercase;
        });
    }

    /*
    xkpasswd -c 0 => oecology_headroom
    xkpasswd -c 1 => bemingled_loll
    xkpasswd -c 2 => repertorial-papillulate-preceptorship
    xkpasswd -c 3 => platemaker.disembroiling.reflectorized.65
    xkpasswd -c 4 => panically.aspect.machicolations.purport.21
    xkpasswd -c 5 => psychobiological*denaturise*deciphers*dehydrogenises*cufflink*19
    xkpasswd -c 6 => 85?ILEAC?noria?SWEEPINGNESS?anthozoans?NECKCLOTH?67
    xkpasswd -c 7 => 21:MATTIFYING:rizzer:STATELINESSES:equivocations:PARAMASTOID:53
    */
    public function testGeneratesComplexitySteps()
    {
        $complexityLevels = array(2, 2, 3, 4, 5, 6, 7, 7);

        // Use index for complexity level.
        // Value is the expected number of parts in the password
        array_walk($complexityLevels, function ($partsExpected, $complexity) {
            $password = PasswordGenerator::generate([
                'complexity' => $complexity
            ]);

            $passwordParts = count($this->splitIntoParts($password));

            $this->assertEquals(
                $passwordParts,
                $partsExpected,
                "Password Complexity of $complexity does not produce $partsExpected parts"
            );
        });
    }

    // * 1) "words.json"
    // * 2) "words.txt"
    // * 3) "orange,banana, fizz, buzz" (string of comma-separated words)
    // * 4) ["orange", "banana", "fizz", "buzz"] (array of words)
    public function testCustomTextFile()
    {
        $testFile = './tests/testWordList.txt';

        $testWordList = explode("\n", file_get_contents($testFile));

        $password = PasswordGenerator::generate([
            'wordList' => $testFile,
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) use ($testWordList) {
            $this->assertContains($part, $testWordList, "Custom word list is not used");
        });
    }

    public function testCustomJsonFile()
    {
        $testFile = './tests/testWordList.json';

        $testWordList = json_decode(file_get_contents($testFile), true);

        $password = PasswordGenerator::generate([
            'wordList' => $testFile,
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) use ($testWordList) {
            $this->assertContains($part, $testWordList, "Custom word list is not used");
        });
    }

    public function testCustomList()
    {
        $testWordList = ["banana", "apple", "orange", "kiwi", "grape", "strawberry"];

        $password = PasswordGenerator::generate([
            'wordList' => implode(',', $testWordList),
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) use ($testWordList) {
            $this->assertContains($part, $testWordList, "Custom word list is not used");
        });
    }

    public function testCustomArray()
    {
        $testWordList = ["banana", "apple", "orange", "kiwi", "grape", "strawberry"];

        $password = PasswordGenerator::generate([
            'wordList' => $testWordList,
        ]);

        $passwordParts = $this->splitIntoParts($password);

        array_walk($passwordParts, function ($part) use ($testWordList) {
            $this->assertContains($part, $testWordList, "Custom word list is not used");
        });
    }

    private function splitIntoParts($password) {
        return preg_split('/[^a-z]+/i', $password);
    }
}
