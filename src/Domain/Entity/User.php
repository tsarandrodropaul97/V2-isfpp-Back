<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;
    private string $email;
    private array $roles = [];
    private string $password;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private bool $isActive = true;
    private ?DateTimeImmutable $lastLoginAt = null;
    private int $loginAttempts = 0;
    private ?DateTimeImmutable $lockedUntil = null;

    public function __construct(
        string $email,
        string $password,
        array $roles = ['ROLE_ADMIN'],
        ?string $firstName = null,
        ?string $lastName = null
    ) {
        $this->id = Uuid::v4();
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function getLoginAttempts(): int
    {
        return $this->loginAttempts;
    }

    public function getLockedUntil(): ?DateTimeImmutable
    {
        return $this->lockedUntil;
    }

    public function setLastLoginAt(?DateTimeImmutable $lastLoginAt): void
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function incrementLoginAttempts(): void
    {
        $this->loginAttempts++;
    }

    public function resetLoginAttempts(): void
    {
        $this->loginAttempts = 0;
        $this->lockedUntil = null;
    }

    public function setLockedUntil(?DateTimeImmutable $lockedUntil): void
    {
        $this->lockedUntil = $lockedUntil;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
