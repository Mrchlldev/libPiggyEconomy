<?php

declare(strict_types=1);

namespace DaPigGuy\libPiggyEconomy;

use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use DaPigGuy\libPiggyEconomy\providers\BedrockEconomyProvider;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use DaPigGuy\libPiggyEconomy\providers\EconomySProvider;
use DaPigGuy\libPiggyEconomy\providers\CandyProvider;
use DaPigGuy\libPiggyEconomy\providers\XPProvider;

class libPiggyEconomy
{
    public static bool $hasInitiated = false;

    /**
     * @var string[] $economyProviders
     * @phpstan-var array<class-string<EconomyProvider>>
     */
    public static array $economyProviders;

    public static function init(): void
    {
        if (!self::$hasInitiated) {
            self::$hasInitiated = true;

            self::registerProvider(["economys", "economyapi"], EconomySProvider::class);
            self::registerProvider(["candy", "candyapi"], CandyProvider::class);
            self::registerProvider(["bedrockeconomy"], BedrockEconomyProvider::class);
            self::registerProvider(["xp", "exp", "experience"], XPProvider::class);
        }
    }

    /**
     * @phpstan-param class-string<EconomyProvider> $economyProvider
     */
    public static function registerProvider(array $providerNames, string $economyProvider): void
    {
        foreach ($providerNames as $providerName) {
            if (isset(self::$economyProviders[strtolower($providerName)])) continue;
            self::$economyProviders[strtolower($providerName)] = $economyProvider;
        }
    }

    /**
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     */
    public static function getProvider(array $providerInformation): EconomyProvider
    {
        if (!isset(self::$economyProviders[strtolower($providerInformation["provider"])])) {
            throw new UnknownProviderException("Provider " . $providerInformation["provider"] . " not found.");
        }
        $provider = self::$economyProviders[strtolower($providerInformation["provider"])];
        if (!$provider::checkDependencies()) {
            throw new MissingProviderDependencyException("Dependencies for provider " . $providerInformation["provider"] . " not found.");
        }
        return new $provider($providerInformation);
    }
}