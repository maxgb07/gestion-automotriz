<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidadorRfc implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rfc = strtoupper(str_replace([' ', '-'], '', $value));
        
        // Regex para RFC de México (Personas Físicas y Morales)
        $regex = '/^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A-Z\d])$/';

        if (!preg_match($regex, $rfc)) {
            $fail('El RFC proporcionado no tiene un formato válido según las reglas del SAT.');
        }
    }
}
