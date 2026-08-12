<?php

declare(strict_types=1);

namespace Tests\Unit\Validation;

use App\Exceptions\ValidationException;
use App\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidDataPasses(): void
    {
        $validator = (new Validator())
            ->required('email', 'user@example.com')
            ->email('email', 'user@example.com')
            ->numericRange('peso', 80, 1, 500);

        $validator->validate();
        self::assertSame([], $validator->errors());
    }

    public function testInvalidDataThrowsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        (new Validator())->required('nome', '')->validate();
    }
}
