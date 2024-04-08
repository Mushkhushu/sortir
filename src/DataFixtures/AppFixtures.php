<?php

namespace App\DataFixtures;

use AllowDynamicProperties;
use App\Entity\Etat;
use App\Factory\EtatFactory;
use App\Factory\LieuFactory;
use App\Factory\SortieFactory;
use App\Factory\SiteFactory;
use App\Factory\UserFactory;
use App\Factory\VilleFactory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

#[AllowDynamicProperties]
class AppFixtures extends Fixture
{

    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createState = EtatFactory::createOne([
            'id' => Etat::ETAT_CREEE,
            'libelle' => 'Créée'
        ]);
        $this->openState = EtatFactory::createOne([
            'id' => Etat::ETAT_OUVERTE,
            'libelle' => 'Ouverte'
        ]);
        $this->closedState = EtatFactory::createOne([
            'id' => Etat::ETAT_CLOTUREE,
            'libelle' => 'Clôturée'
        ]);
        $this->ongoingState = EtatFactory::createOne([
            'id' => Etat::ETAT_EN_COURS,
            'libelle' => 'Activité en cours'
        ]);
        $this->finishState = EtatFactory::createOne([
            'id' => Etat::ETAT_PASSEE,
            'libelle' => 'Passée'
        ]);
        $this->cancelState = EtatFactory::createOne([
            'id' => Etat::ETAT_ANNULEE,
            'libelle' => 'Annulée'
        ]);
        EtatFactory::createOne([
            'id' => Etat::ETAT_ARCHIVEE,
            'libelle' => 'Archivée'
        ]);

        VilleFactory::createMany(6);
        LieuFactory::createMany(20);

        SiteFactory::createOne(['nom' => 'ENI Rennes']);
        SiteFactory::createOne(['nom' => 'ENI Nantes']);
        SiteFactory::createOne(['nom' => 'ENI Quimper']);
        SiteFactory::createOne(['nom' => 'ENI Saint-Herblain']);

        UserFactory::createOne([
            'mail' => 'admin@test.local', 'roles' => ['ROLE_ADMIN'],
            'username' => 'admin',
            'firstName' => 'admin',
            'lastName' => 'admin',
        ]);
        $user = UserFactory::createOne([
            'mail' => 'user@test.local', 'roles' => ['ROLE_USER'],
            'username' => 'user',
            'firstName' => 'user',
            'lastName' => 'user',
        ]);
        UserFactory::createOne([
            'mail' => 'inactive@test.local', 'roles' => ['ROLE_USER'],
            'username' => 'inactive',
            'firstName' => 'inactive',
            'lastName' => 'inactive',
            'active' => false,
            'banExpirationDate' => DateTimeImmutable::createFromMutable(UserFactory::faker()->dateTimeBetween('+1week', '+1month')),
        ]);
        UserFactory::createMany(40);

        foreach ($this->createMeetings() as $sortie) {
            SortieFactory::createMany(3, $sortie);
            $sortiesToOrganize[] = SortieFactory::createMany(2, $sortie);
            foreach ($sortiesToOrganize as $sortieToOrganize) {
                $sortieToOrganize[0]->setOrganizator($user->object());
                $sortieToOrganize[1]->setOrganizator($user->object());
            }
        }
    }

    private function createMeetings(): array
    {
        return [
            // sortie créée
            fn() => [
                'nom' => 'sortie créée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+1month')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->createState
            ],
            // sortie créée mais passée
            fn() => [
                'nom' => 'sortie créée passée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('-2weeks', 'now')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->createState
            ],
            // sortie créée mais archivée
            fn() => [
                'nom' => 'sortie créée passée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween( '-3months', '-1months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->createState
            ],
            // sortie ouverte
            fn() => [
                'nom' => 'sortie ouverte',
                'dateLimite' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+2months')),
                'date' => $date->modify('+5days'),
                'etat' => $this->openState
            ],
            // sortie cloturé futur
            fn() => [
                'nom' => 'sortie cloturé futur',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+2months')),
                'dateLimite' => DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('-2weeks')),
                'etat' => $this->openState
            ],
            // sortie en cours
            fn() => [
                'nom' => 'sortie en cours',
                'date' => $date = DateTimeImmutable::createFromMutable(new \DateTime()),
                'dateLimite' => $date->modify('-5days'),
                'duree' => 9999,
                'etat' => $this->openState
            ],
            // sortie passée
            fn() => [
                'nom' => 'sortie passée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween( '-1months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->openState
            ],
            // sortie archivée
            fn() => [
                'nom' => 'sortie archivée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween( '-3months', '-1months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->openState
            ],
            // sortie annulée futur
            fn() => [
                'nom' => 'sortie annulée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+2months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->cancelState
            ],
            // sortie annulée passée
            fn() => [
                'nom' => 'sortie annulée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween( '-1months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->cancelState
            ],
            // sortie annulée & archivée
            fn() => [
                'nom' => 'sortie annulée & archivée',
                'date' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween( '-3months', '-1months')),
                'dateLimite' => $date->modify('-5days'),
                'etat' => $this->cancelState
            ],
            // sortie futur ouverte mais complète
            fn() => [
                'nom' => 'sortie rempli',
                'dateLimite' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+2months')),
                'date' => $date->modify('+5days'),
                'etat' => $this->openState,
                'nbrPersonne' => $nb = 2,
                'participants' => UserFactory::randomRange($nb, $nb)
            ],
            // sortie futur ouverte mais  presque complète
            fn () => [
                'nom' => 'sortie presque rempli',
                'dateLimite' => $date = DateTimeImmutable::createFromMutable(SortieFactory::faker()->dateTimeBetween('now', '+2months')),
                'date' => $date->modify('+5days'),
                'etat' => $this->openState,
                'nbrPersonne' => $nb = 2,
                'participants' => UserFactory::randomRange($nb-1, $nb-1)
            ],

        ];
    }
}
