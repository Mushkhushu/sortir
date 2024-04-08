<?php

namespace App\Factory;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Sortie>
 *
 * @method        Sortie|Proxy                     create(array|callable $attributes = [])
 * @method static Sortie|Proxy                     createOne(array $attributes = [])
 * @method static Sortie|Proxy                     find(object|array|mixed $criteria)
 * @method static Sortie|Proxy                     findOrCreate(array $attributes)
 * @method static Sortie|Proxy                     first(string $sortedField = 'id')
 * @method static Sortie|Proxy                     last(string $sortedField = 'id')
 * @method static Sortie|Proxy                     random(array $attributes = [])
 * @method static Sortie|Proxy                     randomOrCreate(array $attributes = [])
 * @method static SortieRepository|RepositoryProxy repository()
 * @method static Sortie[]|Proxy[]                 all()
 * @method static Sortie[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Sortie[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Sortie[]|Proxy[]                 findBy(array $attributes)
 * @method static Sortie[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Sortie[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class SortieFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private EtatRepository $stateRepository)
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
        $defaultState = self::faker()->optional(0.7, EtatFactory::find(['id' => Etat::ETAT_CREEE]))
            ->passthrough(EtatFactory::find(['id' => Etat::ETAT_OUVERTE]));
        /** @var State $state */
        $state = self::faker()->optional(0.8, EtatFactory::find(['id' => Etat::ETAT_ANNULEE]))
            ->passthrough($defaultState);

        $registerLimitDateFutur = DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('now', '+2month'));
        $registerLimitDatePast = DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2month'));
        /** @var DateTimeImmutable $registerLimitDate */
        $registerLimitDate = self::faker()->optional(0.7, $registerLimitDatePast)->passthrough($registerLimitDateFutur);

        $maxPeople = self::faker()->randomNumber(2) ?? 1;
        $maxParticipants = $maxPeople > UserFactory::count() ? UserFactory::count() : $maxPeople;
        return [
            'nom' => self::faker()->words(2, true),
            'duree' => self::faker()->randomNumber(2),
            'nbrPersonne' => $maxPeople,
            'dateLimite' => $registerLimitDate,
            'date' => $registerLimitDate->modify('+' . self::faker()->numberBetween(0, 30) . 'days'),
            'note' => self::faker()->text(255),
            'Lieu' => LieuFactory::random(),
            'organizator' => UserFactory::random(),
            'participants' => $state->getLibelle() !== Etat::ETAT_CREEE ? UserFactory::randomRange(0, $maxParticipants) : [],
            'etat' => $state
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (Sortie $sortie): void {
                // update de l'état en fonction du nb de participants et de l'état courant
                if (
                    count($sortie->getParticipants()) >= $sortie->getNbrPersonne() &&
                    !in_array($sortie->getEtat()->getLibelle(), [
                        Etat::ETAT_ANNULEE,
                        Etat::ETAT_EN_COURS,
                        Etat::ETAT_PASSEE,
                        Etat::ETAT_CREEE,
                    ])
                ) {
                    $sortie->setEtat($this->stateRepository->find(Etat::ETAT_CLOTUREE));
                }
            });
    }

    protected static function getClass(): string
    {
        return Sortie::class;
    }
}
