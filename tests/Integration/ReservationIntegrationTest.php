<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

class ReservationIntegrationTest extends TestCase
{
    private Capsule $capsule;
    private EloquentSalleRepository $salleRepo;
    private EloquentReservationRepository $reservationRepo;

    protected function setUp(): void
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        $schema = $this->capsule->schema();

        $schema->create('salles', function ($table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('batiment');
            $table->integer('capacite');
            $table->string('type');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $schema->create('reservations', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('salle_id');
            $table->string('responsable');
            $table->string('email');
            $table->string('motif');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('statut')->default('confirmee');
            $table->timestamps();
        });

        $this->salleRepo = new EloquentSalleRepository();
        $this->reservationRepo = new EloquentReservationRepository();
    }

    public function test_creation_d_une_salle_avec_eloquent(): void
    {
        $salle = $this->salleRepo->creer([
            'nom'      => 'Amphitheatre A',
            'batiment' => 'Batiment Central',
            'capacite' => 250,
            'type'     => 'amphitheatre',
            'active'   => true,
        ]);

        self::assertNotNull($salle->id);
        self::assertSame('Amphitheatre A', $salle->nom);
        self::assertTrue($salle->active);

        $trouvee = $this->salleRepo->trouver($salle->id);
        self::assertNotNull($trouvee);
        self::assertSame('Amphitheatre A', $trouvee->nom);
    }

    public function test_relation_salle_et_reservations(): void
    {
        $salle = $this->salleRepo->creer([
            'nom'      => 'Salle B12',
            'batiment' => 'Batiment B',
            'capacite' => 40,
            'type'     => 'cours',
            'active'   => true,
        ]);

        $res = $this->reservationRepo->creer([
            'salle_id'    => $salle->id,
            'responsable' => 'Awa Ndiaye',
            'email'       => 'awa.ndiaye@universite.sn',
            'motif'       => "Cours d'architecture",
            'date_debut'  => '2027-01-10 10:00:00',
            'date_fin'    => '2027-01-10 12:00:00',
            'statut'      => 'confirmee',
        ]);

        self::assertCount(1, $salle->reservations);
        self::assertSame($res->id, $salle->reservations->first()->id);

        self::assertNotNull($res->salle);
        self::assertSame($salle->id, $res->salle->id);
    }

    public function test_recherche_de_chevauchement(): void
    {
        $salle = $this->salleRepo->creer([
            'nom'      => 'Salle Info 1',
            'batiment' => 'Batiment C',
            'capacite' => 30,
            'type'     => 'informatique',
            'active'   => true,
        ]);

        $this->reservationRepo->creer([
            'salle_id'    => $salle->id,
            'responsable' => 'Dr. Alpha',
            'email'       => 'alpha@universite.sn',
            'motif'       => 'TP Reseau',
            'date_debut'  => '2027-01-10 10:00:00',
            'date_fin'    => '2027-01-10 12:00:00',
            'statut'      => 'confirmee',
        ]);

        $debutChevauche = new DateTimeImmutable('2027-01-10 11:00:00');
        $finChevauche = new DateTimeImmutable('2027-01-10 13:00:00');
        $conflits = $this->reservationRepo->trouverParSalleEtPeriode($salle->id, $debutChevauche, $finChevauche);
        self::assertCount(1, $conflits);

        $debutVoisin = new DateTimeImmutable('2027-01-10 12:00:00');
        $finVoisin = new DateTimeImmutable('2027-01-10 14:00:00');
        $voisins = $this->reservationRepo->trouverParSalleEtPeriode($salle->id, $debutVoisin, $finVoisin);
        self::assertCount(0, $voisins);
    }

    public function test_annulation_d_une_reservation(): void
    {
        $salle = $this->salleRepo->creer([
            'nom'      => 'Reunion 1',
            'batiment' => 'Direction',
            'capacite' => 12,
            'type'     => 'reunion',
            'active'   => true,
        ]);

        $res = $this->reservationRepo->creer([
            'salle_id'    => $salle->id,
            'responsable' => 'Directeur',
            'email'       => 'direction@universite.sn',
            'motif'       => 'Conseil de departement',
            'date_debut'  => '2027-01-10 09:00:00',
            'date_fin'    => '2027-01-10 11:00:00',
            'statut'      => 'confirmee',
        ]);

        self::assertTrue($res->estConfirmee());

        $resAnnulee = $this->reservationRepo->annuler($res);
        self::assertTrue($resAnnulee->estAnnulee());
        self::assertSame('annulee', $this->reservationRepo->trouver($res->id)->statut);

        $debut = new DateTimeImmutable('2027-01-10 09:00:00');
        $fin = new DateTimeImmutable('2027-01-10 11:00:00');
        $conflits = $this->reservationRepo->trouverParSalleEtPeriode($salle->id, $debut, $fin);
        self::assertCount(0, $conflits);
    }
}
