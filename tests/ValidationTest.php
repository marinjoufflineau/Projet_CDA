<?php
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testPasswordHashIsValid(): void
    {
        $hash = password_hash('admin', PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('admin', $hash));
    }

    public function testVoteValueMustBeOneOrMinusOne(): void
    {
        $this->assertContains(1, [-1, 1]);
        $this->assertContains(-1, [-1, 1]);
        $this->assertNotContains(0, [-1, 1]);
    }

    public function testCommentMustNotBeEmpty(): void
    {
        $this->assertNotSame('', trim('Commentaire valide'));
    }
}
