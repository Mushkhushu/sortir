<?php

namespace App\Factory;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Lieu>
 *
 * @method        Lieu|Proxy                     create(array|callable $attributes = [])
 * @method static Lieu|Proxy                     createOne(array $attributes = [])
 * @method static Lieu|Proxy                     find(object|array|mixed $criteria)
 * @method static Lieu|Proxy                     findOrCreate(array $attributes)
 * @method static Lieu|Proxy                     first(string $sortedField = 'id')
 * @method static Lieu|Proxy                     last(string $sortedField = 'id')
 * @method static Lieu|Proxy                     random(array $attributes = [])
 * @method static Lieu|Proxy                     randomOrCreate(array $attributes = [])
 * @method static LieuRepository|RepositoryProxy repository()
 * @method static Lieu[]|Proxy[]                 all()
 * @method static Lieu[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Lieu[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Lieu[]|Proxy[]                 findBy(array $attributes)
 * @method static Lieu[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Lieu[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class LieuFactory extends ModelFactory
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
            'Longitude' => self::faker()->longitude(),
            'latitude' => self::faker()->latitude(),
            'nom' => self::faker()->words(2, true),
            'rue' => self::faker()->streetAddress(),
            'ville' => VilleFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Lieu $lieu): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Lieu::class;
    }
}
