<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\HashedPassword;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;
use Daikon\Entity\Entity\Entity;
use Daikon\Entity\Entity\NestedEntityList;
use Daikon\Entity\ValueObject\Email;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AccountEntity extends Entity
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->get("identity");
    }

    /**
     * @param AggregateId $aggregateId
     * @return AccountEntity
     */
    public function withIdentity(AggregateId $aggregateId): self
    {
        return $this->withValue("identity", $aggregateId);
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->get("email");
    }

    /**
     * @param Email $email
     * @return AccountEntity
     */
    public function withEmail(Email $email): self
    {
        return $this->withValue("email", $email);
    }

    /**
     * @return Username
     */
    public function getUsername(): Username
    {
        return $this->get("username");
    }

    /**
     * @param Username $username
     * @return AccountEntity
     */
    public function withUsername(Username $username): self
    {
        return $this->withValue("username", $username);
    }

    /**
     * @return AccessRole
     */
    public function getRole(): AccessRole
    {
        return $this->get("role");
    }

    /**
     * @param AccessRole $role
     * @return AccountEntity
     */
    public function withRole(AccessRole $role): self
    {
        return $this->withValue("role", $role);
    }

    /**
     * @return Text
     */
    public function getFirstname(): Text
    {
        return $this->get("firstname");
    }

    /**
     * @param Text $firstname
     * @return AccountEntity
     */
    public function withFirstname(Text $firstname): self
    {
        return $this->withValue("firstname", $firstname);
    }

    /**
     * @return Text
     */
    public function getLastname(): Text
    {
        return $this->get("lastname");
    }

    /**
     * @param Text $lastname
     * @return AccountEntity
     */
    public function withLastname(Text $lastname): self
    {
        return $this->withValue("lastname", $lastname);
    }

    /**
     * @return HashedPassword
     */
    public function getPasswordHash(): HashedPassword
    {
        return $this->get("password_hash");
    }

    /**
     * @param HashedPassword $passwordHash
     * @return AccountEntity
     */
    public function withPasswordHash(HashedPassword $passwordHash): self
    {
        return $this->withValue("passwordHash", $passwordHash);
    }

    /**
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->get("locale");
    }

    /**
     * @param Locale $locale
     * @return AccountEntity
     */
    public function withLocale(Locale $locale): self
    {
        return $this->withValue("locale", $locale);
    }

    /**
     * @return NestedEntityList
     */
    public function getTokens(): NestedEntityList
    {
        return $this->get("tokens");
    }

    /**
     * @param array $tokenPayload
     * @return AccountEntity
     */
    public function addAuthenticationToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "authentication_token");
    }

    /**
     * @param array $tokenPayload
     * @return AccountEntity
     */
    public function addVerificationToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "verification_token");
    }

    /**
     * @param array $tokenPayload
     * @return AccountEntity
     */
    public function addOauthToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "oauth_token");
    }

    /**
     * @param array $tokenPayload
     * @return AccountEntity
     */
    public function addPasswordToken(array $tokenPayload): self
    {
        return $this->addToken($tokenPayload, "password_token");
    }

    /**
     * @param array $tokenPayload
     * @param string $type
     * @return AccountEntity
     */
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
