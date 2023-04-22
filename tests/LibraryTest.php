<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class LibraryTest extends TestCaseSymconValidation
{
    public function testValidateLibrary(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateUniversalTrigger(): void
    {
        $this->validateModule(__DIR__ . '/../UniversalTrigger');
    }

    public function testValidateUniversalTriggerGroup(): void
    {
        $this->validateModule(__DIR__ . '/../UniversalTriggerGroup');
    }
}