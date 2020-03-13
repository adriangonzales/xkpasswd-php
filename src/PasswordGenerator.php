<?php

namespace Adgodev\Xkpasswd;

use Exception;

class PasswordGenerator
{
    /**
     * Path to the default word list
     *
     * @var array
     */
    public static $defaultWordList = './xkpasswd-words.json';

    /**
     * Generate password with options
     *
     * @return string
     */
    public static function generate($opts = [])
    {
        $opts = self::processOpts($opts);
        $pattern = str_split($opts->pattern);
        $uppercase = $opts->transform === 'uppercase';
        $password = [];

        $wordList = self::readWordList($opts->wordList);

        $password = array_map(function ($type) use (&$uppercase, $opts, $wordList) {
            if ($type === 'd') $value = random_int(0, 9);
            if ($type === 's') $value = $opts->separator;
            if ($type === 'w' || $type === 'W') {
                $value = self::getRandomWord($wordList);

                if ($opts->transform === 'alternate') {
                    $uppercase = !$uppercase;
                }

                if ($uppercase || $type === 'W') {
                    $value = strtoupper($value);
                } else {
                    $value = strtolower($value);
                }
            }

            return $value;
        }, $pattern);

        return implode('', $password);
    }

    /**
     * Retrieve a random word from the given list
     *
     * @param  array $wordList Array of words to use
     * @return string
     */
    private static function getRandomWord($wordList)
    {
        return $wordList[random_int(0, count($wordList) - 1)];
    }

    /**
     * Generate a pattern based on a predefined level of complexity
     *
     * @param  int $complexity Level of complexity 1-6
     * @return object
     */
    private static function resolveComplexity($complexity = 2)
    {
        $returnComplexity = [];
        $returnComplexity['transform'] = 'lowercase';
        $returnComplexity['separators'] = '#.-=+_';
        if ($complexity < 1) $complexity = 1;
        if ($complexity > 6) $complexity = 6;

        if ($complexity === 1) $returnComplexity['pattern'] = 'wsw';
        if ($complexity === 2) $returnComplexity['pattern'] = 'wswsw';
        if ($complexity === 3) $returnComplexity['pattern'] = 'wswswsdd';
        if ($complexity === 4) $returnComplexity['pattern'] = 'wswswswsdd';

        if ($complexity === 5) {
            $returnComplexity['pattern'] = 'wswswswswsdd';
            $returnComplexity['separators'] = '#.-=+_!$*:~?';
        }

        if ($complexity === 6) {
            $returnComplexity['pattern'] = 'ddswswswswswsdd';
            $returnComplexity['transform'] = 'alternate';
            $returnComplexity['separators'] = '#.-=+_!$*:~?%^&;';
        }

        return (object) $returnComplexity;
    }

    /**
     * Process an array of options
     *
     * @param  array  $opts Level of complexity 1-6
     * @return object
     */
    private static function processOpts($opts)
    {
        $complexity = 2;
        if (array_key_exists('complexity', $opts)) {
            $complexity = intval($opts['complexity'], 10);
        }

        $predefined = self::resolveComplexity($complexity);

        if (array_key_exists('separators', $opts) && is_string($opts['separators'])) {
            $separators = $opts['separators'];
        } else {
            $separators = $predefined->separators;
        }

        $returnOptions = [
            'pattern' => $predefined->pattern,
            'transform' => $predefined->transform,
            'wordList' => self::$defaultWordList,
            'separator' => $separators
        ];

        if (strlen($separators) > 1) {
            $randomIndex = random_int(0, strlen($separators) - 1);
            $returnOptions['separator'] = str_split($separators)[$randomIndex];
        }

        if (array_key_exists('pattern', $opts)) {
            $returnOptions['pattern'] = $opts['pattern'];
        }

        if (array_key_exists('transform', $opts)) {
            $returnOptions['transform'] = strtolower($opts['transform']);
        }

        if (array_key_exists('wordList', $opts)) {
            $returnOptions['wordList'] = $opts['wordList'];
        }

        return (object)$returnOptions;
    }

    /**
     * Process an array of options
     * this needs to support the following options:
     * 1) "words.json"
     * 2) "words.txt"
     * 3) "orange,banana, fizz, buzz" (string of comma-separated words)
     * 4) ["orange", "banana", "fizz", "buzz"] (array of words)
     *
     * @param  mixed  $input Filename or list of words
     * @return object
     */
    private static function readWordList($input)
    {
        $data = false;

        if (is_array($input)) {
            $data = $input;
        }

        // parse string input
        if (is_string($input)) {
            $tmpWordList = explode(',', $input);

            // One word, assume filename
            if (count($tmpWordList) === 1) {
                $targetFile = realpath($tmpWordList[0]);

                if (file_exists($targetFile)) {
                    $fileContents = file_get_contents($targetFile);

                    // Process JSON
                    if (strrpos($targetFile, '.json') === strlen($targetFile) - 5) {
                        $data = json_decode($fileContents, true);
                    }

                    if (strrpos($targetFile, '.txt') === strlen($targetFile) - 4) {
                        $data = explode("\n", $fileContents);
                    }
                } else {
                    throw new Exception("Custom word list file not found");
                }
            }

            // Multiple words, use this as input
            if (!$data) {
                $data = $tmpWordList;
            }
        }

        // if there's no $data return false
        if (!$data) {
            return false;
        }

        // Remove empty and non-strings
        $filteredList= array_filter($data, function ($word) {
            return is_string($word) && strlen($word) > 0;
        });

        return $filteredList;
    }
}
