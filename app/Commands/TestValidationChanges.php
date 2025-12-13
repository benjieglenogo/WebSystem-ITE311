<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestValidationChanges extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:validation-changes';
    protected $description = 'Tests the new validation rules for user creation';

    public function run(array $params)
    {
        CLI::write('Testing New Validation Rules', 'yellow');
        CLI::newLine();

        $validation = \Config\Services::validation();

        // Test 1: Valid name with Spanish characters
        CLI::write('Test 1: Valid name with Spanish characters', 'blue');
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+$/]',
        ]);

        $testData1 = ['name' => 'José María'];
        if ($validation->run($testData1)) {
            CLI::write("✓ 'José María' is valid", 'green');
        } else {
            CLI::write("✗ 'José María' should be valid", 'red');
            CLI::write(implode(', ', $validation->getErrors()), 'white');
        }

        // Test 2: Invalid name with special characters
        CLI::newLine();
        CLI::write('Test 2: Invalid name with special characters', 'blue');
        $testData2 = ['name' => 'John@Doe'];
        if (!$validation->run($testData2)) {
            CLI::write("✓ 'John@Doe' is correctly rejected", 'green');
        } else {
            CLI::write("✗ 'John@Doe' should be rejected", 'red');
        }

        // Test 3: Valid password with letters and numbers only
        CLI::newLine();
        CLI::write('Test 3: Valid password with letters and numbers only', 'blue');
        $validation = \Config\Services::validation(); // Reset validation service
        $validation->setRules([
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ])(?=.*\d)[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\d]+$/]',
        ]);

        $testData3 = ['password' => 'Password123'];
        if ($validation->run($testData3)) {
            CLI::write("✓ 'Password123' is valid", 'green');
        } else {
            CLI::write("✗ 'Password123' should be valid", 'red');
            CLI::write(implode(', ', $validation->getErrors()), 'white');
        }

        // Test 4: Invalid password with special characters
        CLI::newLine();
        CLI::write('Test 4: Invalid password with special characters', 'blue');
        $testData4 = ['password' => 'Password@123'];
        if (!$validation->run($testData4)) {
            CLI::write("✓ 'Password@123' is correctly rejected", 'green');
        } else {
            CLI::write("✗ 'Password@123' should be rejected", 'red');
        }

        // Test 5: Valid password with Spanish characters
        CLI::newLine();
        CLI::write('Test 5: Valid password with Spanish characters', 'blue');
        $testData5 = ['password' => 'Contraseña123'];
        if ($validation->run($testData5)) {
            CLI::write("✓ 'Contraseña123' is valid", 'green');
        } else {
            CLI::write("✗ 'Contraseña123' should be valid", 'red');
            CLI::write(implode(', ', $validation->getErrors()), 'white');
        }

        CLI::newLine();
        CLI::write('All validation tests completed!', 'green');
    }
}
