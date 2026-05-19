<?php
/**
 * Validation Service
 * SOLID: Single Responsibility - শুধুমাত্র validation logic
 * SOLID: Open/Closed - নতুন validation rules add করা যায় without modifying existing code
 */

namespace App\Services;

class ValidationService {
    private array $errors = [];

    /**
     * Validate required field
     */
    public function required(string $field, mixed $value, ?string $message = null): self {
        if (empty(trim((string)$value))) {
            $this->errors[$field] = $message ?? ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
        return $this;
    }

    /**
     * Validate value is in allowed list
     */
    public function inList(string $field, mixed $value, array $allowed, ?string $message = null): self {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = $message ?? ucfirst(str_replace('_', ' ', $field)) . ' has an invalid value.';
        }
        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(string $field, string $value, int $max, ?string $message = null): self {
        if (strlen($value) > $max) {
            $this->errors[$field] = $message ?? ucfirst(str_replace('_', ' ', $field)) . " must be at most {$max} characters.";
        }
        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(string $field, string $value, int $min, ?string $message = null): self {
        if (strlen($value) < $min) {
            $this->errors[$field] = $message ?? ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function email(string $field, string $value, ?string $message = null): self {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Invalid email format.';
        }
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function isValid(): bool {
        return empty($this->errors);
    }

    /**
     * Get all errors
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function firstError(): string {
        return array_values($this->errors)[0] ?? '';
    }

    /**
     * Reset errors
     */
    public function reset(): self {
        $this->errors = [];
        return $this;
    }
}
