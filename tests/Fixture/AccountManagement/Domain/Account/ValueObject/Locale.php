<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class Locale implements ValueObjectInterface
{
    public const ALLOWED_LOCALES = [ "en_US", "de_DE", "it_IT", "es_ES", "fr_FR", "en_GB" ];

    /**
     * @var string
     */
    private $languageCode;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @param string $languageCode
     * @return Locale
     */
    public static function fromNative($nativeState): ValueObjectInterface
    {
        if (is_string($nativeState)) {
            $nativeState = explode("_", $nativeState);
        }
        Assertion::isArray($nativeState);
        return new static(...$nativeState);
    }

    /**
     * @return Locale
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static("", "");
    }

    /**
     * @return string
     */
    public function toNative()
    {
        return [ "language" => $this->languageCode, "country" => $this->countryCode ];
    }

    /**
     * @param ValueObjectInterface $locale
     * @return bool
     */
    public function equals(ValueObjectInterface $locale): bool
    {
        Assertion::isInstanceOf($locale, static::class);
        return $this->toNative() == $locale->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->languageCode) && empty($this->countryCode);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "" : $this->languageCode."_".$this->countryCode;
    }

    /**
     * @param string $languageCode
     * @param string $countryCode
     */
    private function __construct(string $languageCode, string $countryCode)
    {
        $this->languageCode = $languageCode;
        $this->countryCode = $countryCode;
        if (!$this->isEmpty()) {
            Assertion::inArray((string)$this, self::ALLOWED_LOCALES);
        }
    }
}
