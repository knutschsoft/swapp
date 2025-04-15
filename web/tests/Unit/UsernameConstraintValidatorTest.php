<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validator\Constraints\UsernameConstraint;
use App\Validator\Constraints\UsernameConstraintValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UsernameConstraintValidatorTest extends TestCase
{
    private UsernameConstraintValidator $validator;
    private ExecutionContextInterface $context;
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        $this->validator = new UsernameConstraintValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->context->method('buildViolation')->willReturn($this->violationBuilder);
        $this->violationBuilder->method('setParameter')->willReturn($this->violationBuilder);
        $this->violationBuilder->method('addViolation');

        $this->validator->initialize($this->context);
    }

    public static function validUsernamesProvider(): array
    {
        return [
            ['david-ansat.sonntag'],
            ['robert.freigang'],
            ['robertfausk'],
        ];
    }

    public static function invalidUsernamesProvider(): array
    {
        return [
            ['-david'],
            ['robert.'],
            ['.freigang'],
            ['robert-'],
            ['david_ansat'],
            ['robert123'],
            ['123robert'],
        ];
    }

    #[DataProvider('validUsernamesProvider')]
    public function testValidUsernames(string $username): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($username, new UsernameConstraint());
    }

    #[DataProvider('invalidUsernamesProvider')]
    public function testInvalidUsernames(string $username): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->validator->validate($username, new UsernameConstraint());
    }
}
