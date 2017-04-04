<?php

class Readability
{

    private $numWords = 0;
    private $numSentences = 0;
    private $numSyllables = 0;
    private $wordsArray = [];
    private $pattern;

    /**
     * Readability constructor.
     * @param Pattern $pattern
     */
    function __construct(Pattern $pattern)
    {
        $this->pattern = $pattern;
    }


    /**
     * @param $text
     */
    private function words($text)
    {
        preg_match_all("([a-zA-Z]+)", $text, $result);

        foreach ($result[0] as $word) {
            if ( is_string($word) ) {
                $this->wordsArray[] = $word;
            };
        }
    }

    /**
     * @param $text
     */
    private function processText($text) {

        $this->words($text);

        $this->numWords = str_word_count($text);
        $this->numSentences = preg_match_all("([^\.\!\?]+[\.\?\!]*)", $text);


        function patternLookupResult($pattern, $word, &$maxSyllablesPerWorld) {
            $patternLookupResult = preg_match_all('/'.$pattern.'/', $word);
            $case = $maxSyllablesPerWorld < $patternLookupResult;

            if ($case) {
                $maxSyllablesPerWorld = $patternLookupResult;
            }
        }

        foreach ($this->wordsArray as $word) {

            $maxSyllablesPerWorld = 0;

            //case if word special
            if (array_key_exists($word, $this->pattern->problem_words)) {
                $this->numSyllables += $this->pattern->problem_words[$word];
                continue;
            }

            foreach ($this->pattern->subtract_syllable_patterns as $pattern) {
                patternLookupResult($pattern, $word, $maxSyllablesPerWorld);
            }

            foreach ($this->pattern->add_syllable_patterns as $pattern) {
                patternLookupResult($pattern, $word, $maxSyllablesPerWorld);
            }

            foreach ($this->pattern->prefix_and_suffix_patterns as $pattern) {
                patternLookupResult($pattern, $word, $maxSyllablesPerWorld);
            }

            $consecutiveVowels = preg_match_all("/[aeiouy]+/", $word);
            $nonConsecutiveVowels = preg_match_all("/[^aeiouy]*[aeiouy][^aeiouy]*e\\b/", $word);

            if (strrpos($word, strlen($word) - 1) == 'e' && $this->isAVowel(strrpos($word, strlen($word) - 2))) {
                $consecutiveVowels += 1;
            }

            $res = $consecutiveVowels - $nonConsecutiveVowels;

            if ($maxSyllablesPerWorld < $res) {
                $maxSyllablesPerWorld = $res;
            }

            $this->numSyllables += $maxSyllablesPerWorld;


        }

    }

    /**
     * @param $c
     * @return bool
     */
    private function isAVowel($c) {
        return !! strrpos("aeiouy", $c);
    }


    /**
     * @param $writing_sample
     * @return float
     */
    public function ease_score($writing_sample)
       {

           $this->processText($writing_sample);

           return 206.835 - 1.015 * ($this->numWords / $this->numSentences) - 84.6 * ($this->numSyllables / $this->numWords);
       }


}