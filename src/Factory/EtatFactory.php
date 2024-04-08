<?php

namespace App\Factory;

use App\Entity\Etat;
use App\Repository\EtatRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Etat>
 *
 * @method        Etat|Proxy                     create(array|callable $attributes = [])
 * @method static Etat|Proxy                     createOne(array $attributes = [])
 * @method static Etat|Proxy                     find(object|array|mixed $criteria)
 * @method static Etat|Proxy                     findOrCreate(array $attributes)
 * @method static Etat|Proxy                     first(string $sortedField = 'id')
 * @method static Etat|Proxy                     last(string $sortedField = 'id')
 * @method static Etat|Proxy                     random(array $attributes = [])
 * @method static Etat|Proxy                     randomOrCreate(array $attributes = [])
 * @method static EtatRepository|RepositoryProxy repository()
 * @method static Etat[]|Proxy[]                 all()
 * @method static Etat[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Etat[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Etat[]|Proxy[]                 findBy(array $attributes)
 * @method static Etat[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Etat[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class EtatFactory extends ModelFactory
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
            'id' => self::faker()->randomNumber(),
            'libelle' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Etat $etat): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Etat::class;
    }
}
