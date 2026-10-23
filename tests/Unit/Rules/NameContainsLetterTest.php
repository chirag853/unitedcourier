<?php

namespace Tests\Unit\Rules;

use App\Rules\NameContainsLetter;
use PHPUnit\Framework\TestCase;

class NameContainsLetterTest extends TestCase
{
    public function test_it_rejects_values_without_letters(): void
    {
        foreach (['9876543210', '+91 98765 43210', '98765-43210', '12345', '() - .'] as $value) {
            $this->assertTrue($this->fails($value), 'Expected "'.$value.'" to fail validation.');
        }
    }

    public function test_it_allows_alphanumeric_and_unicode_names(): void
    {
        foreach (['ABC Logistics', '3M India', '24x7 Courier', "O'Neil", 'M/S ABC & Co.', 'राहुल', 'John 9876543210'] as $value) {
            $this->assertFalse($this->fails($value), 'Expected "'.$value.'" to pass validation.');
        }
    }

    public function test_it_ignores_empty_values(): void
    {
        $this->assertFalse($this->fails(''));
        $this->assertFalse($this->fails('   '));
    }

    private function fails(string $value): bool
    {
        $failed = false;

        (new NameContainsLetter('Name'))->validate('name', $value, function (string $message) use (&$failed): void {
            $failed = true;
        });

        return $failed;
    }
}
