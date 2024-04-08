<?php

namespace App\Factory;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Ville>
 *
 * @method        Ville|Proxy                     create(array|callable $attributes = [])
 * @method static Ville|Proxy                     createOne(array $attributes = [])
 * @method static Ville|Proxy                     find(object|array|mixed $criteria)
 * @method static Ville|Proxy                     findOrCreate(array $attributes)
 * @method static Ville|Proxy                     first(string $sortedField = 'id')
 * @method static Ville|Proxy                     last(string $sortedField = 'id')
 * @method static Ville|Proxy                     random(array $attributes = [])
 * @method static Ville|Proxy                     randomOrCreate(array $attributes = [])
 * @method static VilleRepository|RepositoryProxy repository()
 * @method static Ville[]|Proxy[]                 all()
 * @method static Ville[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Ville[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Ville[]|Proxy[]                 findBy(array $attributes)
 * @method static Ville[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Ville[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class VilleFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'codePostal' => self::faker()->city(),
            'nom' => self::faker()->postcode(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Ville $ville): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Ville::class;
    }
}
