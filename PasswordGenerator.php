<?php

class PasswordGenerator 
{
    private int $length;
    private int $uppercaseCount;
    private int $lowercaseCount;
    private int $numbersCount;
    private int $specialCount;

    private string $uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
    private string $numberChars = '0123456789';
    private string $specialChars = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    public function __construct(
        int $length = 12, 
        int $uppercaseCount = 3, 
        int $lowercaseCount = 3, 
        int $numbersCount = 3, 
        int $specialCount = 3
    ) {
        $this->length = $length;
        $this->uppercaseCount = $uppercaseCount;
        $this->lowercaseCount = $lowercaseCount;
        $this->numbersCount = $numbersCount;
        $this->specialCount = $specialCount;
    }

    public function generate(): string 
    {
        $passwordCharacters = [];

        // Add requested amounts of each category
        $this->addRandomChars($passwordCharacters, $this->uppercaseChars, $this->uppercaseCount);
        $this->addRandomChars($passwordCharacters, $this->lowercaseChars, $this->lowercaseCount);
        $this->addRandomChars($passwordCharacters, $this->numberChars, $this->numbersCount);
        $this->addRandomChars($passwordCharacters, $this->specialChars, $this->specialCount);

        // Fill remaining length if specified totals are less than full length
        $allChars = $this->uppercaseChars . $this->lowercaseChars . $this->numberChars . $this->specialChars;
        while (count($passwordCharacters) < $this->length) {
            $passwordCharacters[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle securely
        for ($i = count($passwordCharacters) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $temp = $passwordCharacters[$i];
            $passwordCharacters[$i] = $passwordCharacters[$j];
            $passwordCharacters[$j] = $temp;
        }

        return implode('', $passwordCharacters);
    }

    private function addRandomChars(array &$targetArray, string $sourceChars, int $count): void 
    {
        $maxIndex = strlen($sourceChars) - 1;
        for ($i = 0; $i < $count; $i++) {
            $targetArray[] = $sourceChars[random_int(0, $maxIndex)];
        }
    }
}