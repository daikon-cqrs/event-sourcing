<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Entity\Entity\Entity;
use Daikon\Entity\Entity\NestedEntityList;
use Daikon\Entity\ValueObject\Email;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\ValueObjectInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\HashedPassword;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class AccountEntity extends Entity
{
    public function getIdentity(): ValueObjectInterface
    {
        return $this->get("identity");
    }

    public function withIdentity(AggregateId $aggregateId): self
    {
        return $this->withValue("identity", $aggregateId);
    }

    public function getEmail(): Email
    {
        return $this->get("email");
    }

    public function withEmail(Email $email): self
    {
        return $this->withValue("email", $email);
    }

    public function getUsername(): Username
    {
        return $this->get("username");
    }

    public function withUsername(Username $username): self
    {
        return $this->withValue("username", $username);
    }

    public function getRole(): AccessRole
    {
        return $this->get("role");
    }

    public function withRole(AccessRole $role): self
    {
        return $this->withValue("role", $role);
    }

    public function getFirstname(): Text
    {
        return $this->get("firstname");
    }

    public function withFirstname(Text $firstname): self
    {
        return $this->withValue("firstname", $firstname);
    }

    public function getLastname(): Text
    {
        return $this->get("lastname");
    }

    public function withLastname(Text $lastname): self
    {
        return $this->withValue("lastname", $lastname);
    }

    public function getPasswordHash(): HashedPassword
    {
        return $this->get("password_hash");
    }

    public function withPasswordHash(HashedPassword $passwordHash): self
    {
        return $this->withValue("passwordHash", $passwordHash);
    }

    public function getLocale(): Locale
    {
        return $this->get("locale");
    }

    public function withLocale(Locale $locale): self
    {
        return $this->withValue("locale", $locale);
    }

    public function getTokens(): NestedEntityList
    {
        return $this->get("tokens");
    }

    public function addAuthenticationToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "authentication_token");
    }

    public function addVerificationToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "verification_token");
    }

    public function addOauthToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "oauth_token");
    }

    public function addPasswordToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "password_token");
    }

    private function addToken(array $tokenPayload, string $type): self
    {
        /* @var \Daikon\Entity\EntityType\NestedEntityListAttribute $tokenList */
        $tokensAttribute = $this->getEntityType()->getAttribute("tokens");
        $tokenType = $tokensAttribute->getValueType()->get($type);
        /* @var \Daikon\Entity\Entity\NestedEntity $token */
        $token = $tokenType->makeEntity($tokenPayload, $this);
        /* @var AccountEntity $accountState */
        $accountState = $this->withValue("tokens", $this->getTokens()->push($token));
        return $accountState;
    }
}
