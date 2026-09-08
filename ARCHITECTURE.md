
# ARCHITECTURE.md — Analyse des choix architecturaux

Ce document identifie et explique les notions d'architecture logicielle
mobilisées dans le projet **Réservation de salles universitaires**. Pour
chaque notion : les classes concernées, son rôle, un avantage, une limite
ou un risque, et un extrait représentatif du projet.

---

## 1. MVC (Modèle-Vue-Contrôleur)

**Classes concernées** : `App\Model\*`, `templates/**/*.php`, `App\Controller\*`.

**Rôle** : séparer la représentation des données (Modèle), leur affichage
(Vue) et la coordination entre les deux (Contrôleur). Dans ce projet, le
Contrôleur est en réalité un chef d'orchestre fin : il délègue la
validation aux `Validator`, les règles métier aux `Service`, et l'accès
aux données aux `Repository`. Le Modèle Eloquent, lui, ne représente QUE
les données et leurs relations.

**Avantage** : un changement de présentation (nouvelle vue HTML, future
API JSON) n'oblige pas à toucher aux règles métier ; un changement de
règle métier n'oblige pas à toucher aux vues.

**Limite / risque** : le sigle « MVC » est trompeur ici car le
Contrôleur ne contient presque aucune logique — c'est volontaire (voir
Service ci-dessous), mais un débutant peut être tenté d'y remettre de la
logique métier « parce que c'est le contrôleur qui reçoit la requête ».

**Extrait représentatif** (`src/Controller/SalleController.php`) :

```php
public function store(array $params, Request $request): string
{
    $resultat = $this->validator->validate($request->toutesLesDonnees());
    if (!$resultat->isValid()) {
        return $this->view->renderAvecLayout('salle/form', [...]);
    }
    $dto = CreerSalleDTO::depuisTableauValide($resultat->data());
    $salle = $this->salles->enregistrer(new Salle([...]));
    // ...
}
```

---

## 2. Front Controller

**Classes concernées** : `public/index.php`, `App\Application`.

**Rôle** : un point d'entrée HTTP unique reçoit toutes les requêtes
(configuré côté serveur pour rediriger `/*` vers `public/index.php`), au
lieu d'avoir un fichier `.php` par page. `public/index.php` construit le
conteneur, démarre Eloquent, puis délègue à `App\Application::run()`.

**Avantage** : toute requête passe par le même bootstrap (autoloading,
configuration, gestion d'erreurs), ce qui centralise la sécurité et évite
la duplication.

**Limite / risque** : si ce point d'entrée devient trop « intelligent »
(logique métier, accès direct à la base), il devient un god object. Ici,
il ne fait que construire deux objets et appeler une méthode.

**Extrait représentatif** (`public/index.php`) :

```php
$container = $builder->build();
$container->get(Capsule::class);       // démarre Eloquent une fois
$application = $container->get(Application::class);
$application->run();
```

---

## 3. Router (Routeur)

**Classes concernées** : `routes/web.php`, `FastRoute\Dispatcher` (config/container.php), `App\Application`.

**Rôle** : faire correspondre une méthode HTTP + un chemin d'URL à un
couple `[Contrôleur, action]`, avec extraction des paramètres dynamiques
(`{id:\d+}`) et gestion des cas 404/405.

**Avantage** : les URLs de l'application sont déclarées à un seul endroit,
lisible comme une table, indépendamment de l'implémentation des
contrôleurs.

**Limite / risque** : FastRoute est volontairement « bête » : il ne sait
pas construire un contrôleur ni lire ses dépendances. Il faut donc un
composant supplémentaire (ici, le conteneur, appelé depuis
`Application::traiter()`) pour transformer le nom de classe retourné par
FastRoute en instance utilisable.

**Extrait représentatif** (`routes/web.php`) :

```php
$r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
```

---

## 4. Validator (Validateur)

**Classes concernées** : `App\Validation\ValidatorInterface`,
`ValidationResult`, `SalleValidator`, `ReservationValidator`.

**Rôle** : vérifier la forme des données HTTP entrantes (types, longueurs,
formats) avant qu'elles n'atteignent la couche métier, et renvoyer un
verdict structuré (`ValidationResult`) plutôt qu'une exception, pour
permettre un réaffichage de formulaire.

**Avantage** : la validation syntaxique est testable indépendamment de
toute base de données (voir `tests/Unit/SalleValidatorTest.php`), et
réutilisable entre `store()` et `update()`.

**Limite / risque** : la frontière entre validation syntaxique et règle
métier est parfois fine. Ici, la comparaison entre `date_debut` et
`date_fin` est volontairement exclue du validateur et déplacée dans
`CreerReservationService`, car elle dépend de la sémantique métier
(« une réservation ne peut pas durer plus de 4h »), pas seulement du
format d'une date.

**Extrait représentatif** (`src/Validation/ReservationValidator.php`) :

```php
if (!v::email()->validate($email)) {
    $erreurs['email'] = 'Veuillez saisir une adresse électronique valide.';
}
```

---

## 5. DTO (Data Transfer Object)

**Classes concernées** : `App\DTO\CreerSalleDTO`, `CreerReservationDTO`.

**Rôle** : transporter des données déjà validées, sous forme d'un objet
immuable et typé, entre le Contrôleur et le Service — sans jamais exposer
`$_POST` (un simple tableau non typé) à la couche métier.

**Avantage** : le Service qui reçoit un `CreerReservationDTO` sait, par le
système de types de PHP, qu'il manipule un `DateTimeImmutable` et non une
chaîne de caractères — impossible de lui passer accidentellement un
tableau brut.

**Limite / risque** : un DTO ne doit jamais gagner de comportement
métier (pas de méthode `verifierDisponibilite()` dessus) ni de méthode de
persistance (pas de `save()`) : il redeviendrait un modèle déguisé.

**Extrait représentatif** (`src/DTO/CreerReservationDTO.php`) :

```php
final class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly DateTimeImmutable $dateDebut,
        public readonly DateTimeImmutable $dateFin,
        // ...
    ) {}
}
```

---

## 6. ORM et Active Record

**Classes concernées** : `App\Model\Salle`, `App\Model\Reservation`
(via `Illuminate\Database\Eloquent\Model`).

**Rôle** : un ORM (Object-Relational Mapper) traduit des lignes de table
SQL en objets PHP et inversement. Eloquent utilise spécifiquement le
patron **Active Record** : chaque instance de modèle sait se sauvegarder
elle-même (`$salle->save()`), contrairement à un patron Data Mapper où un
objet séparé s'en chargerait.

**Avantage** : productivité (pas de SQL à écrire pour les opérations
courantes), lisibilité (`$salle->reservations` plutôt qu'une jointure
manuelle), portabilité partielle entre moteurs SQL.

**Limite / risque** : Active Record couple le modèle à la couche de
persistance — un `Salle` « est » une ligne de base de données, ce qui
peut poser problème pour des règles métier complexes (d'où la couche
Service, qui garde la logique en dehors du modèle) et rend les tests
unitaires du modèle plus difficiles sans base réelle (d'où les doublures
`InMemory*Repository`, qui n'utilisent PAS Eloquent).

**Extrait représentatif** (`src/Model/Salle.php`) :

```php
final class Salle extends Model
{
    protected $fillable = ['nom', 'batiment', 'capacite', 'type', 'active'];
    protected $casts = ['capacite' => 'integer', 'active' => 'boolean'];
    public function reservations(): HasMany { return $this->hasMany(Reservation::class); }
}
```

---

## 7. Repository

**Classes concernées** : `SalleRepositoryInterface`,
`ReservationRepositoryInterface`, `EloquentSalleRepository`,
`EloquentReservationRepository`.

**Rôle** : offrir à la couche métier un vocabulaire orienté domaine
(`trouver()`, `rechercherConflits()`) plutôt que le vocabulaire d'Eloquent
(`::query()->where(...)`). Le Repository est la SEULE couche autorisée à
appeler Eloquent directement.

**Avantage** : les Services et Contrôleurs dépendent d'une *interface*,
jamais d'Eloquent. On peut donc fournir une implémentation en mémoire
dans les tests (`tests/Support/InMemoryReservationRepository.php`) et
tester les 8 scénarios de règles métier sans MySQL.

**Limite / risque** : Eloquent constitue *déjà* un accès aux données
(patron Active Record) — ajouter un Repository par-dessus est donc une
couche d'abstraction supplémentaire, pas strictement indispensable pour
une application aussi simple. Elle se justifie ici par un objectif
pédagogique (testabilité, découplage) et resterait utile si l'équipe
voulait un jour changer d'ORM sans toucher aux services.

**Extrait représentatif** (`src/Repository/EloquentReservationRepository.php`) :

```php
public function rechercherConflits(int $salleId, DateTimeImmutable $debut, DateTimeImmutable $fin, ?int $exclureReservationId = null): array
{
    return Reservation::query()
        ->where('salle_id', $salleId)
        ->where('statut', Reservation::STATUT_CONFIRMEE)
        ->where('date_debut', '<', $fin)
        ->where('date_fin', '>', $debut)
        ->get()->all();
}
```

---

## 8. Service

**Classes concernées** : `CreerReservationService`, `AnnulerReservationService`.

**Rôle** : porter les règles métier qui n'appartiennent ni au
Contrôleur (qui orchestre HTTP), ni au Modèle (qui représente les
données), ni au Repository (qui ne fait qu'accéder aux données) : ordre
des dates, durée maximale, caractère futur, recherche de conflit.

**Avantage** : ces règles, rassemblées à un seul endroit, sont testables
unitairement (voir `tests/Unit/CreerReservationServiceTest.php`) sans
navigateur ni base de données, et réutilisables si l'application gagne un
jour une API JSON en plus des formulaires HTML.

**Limite / risque** : un Service peut devenir un « God Service » s'il
accumule trop de responsabilités. Ici, il reste focalisé sur UNE
opération métier par classe (créer, annuler), ce qui facilite sa lecture
et son test.

**Extrait représentatif** (`src/Service/CreerReservationService.php`) :

```php
if ($this->reservations->rechercherConflits($salle->id, $dto->dateDebut, $dto->dateFin) !== []) {
    throw SalleIndisponibleException::conflit($salle->nom);
}
```

---

## 9. Injection par constructeur

**Classes concernées** : à peu près toutes (`CreerReservationService`,
`SalleController`, `ReservationController`, `App\Application`...).

**Rôle** : chaque classe déclare ses dépendances comme paramètres
*typés* de son constructeur, plutôt que de les créer elle-même (`new EloquentSalleRepository()`) ou d'aller les chercher dans un registre
global.

**Avantage** : les dépendances sont explicites (visibles dans la
signature), remplaçables (on peut injecter une doublure de test), et la
classe ne connaît que des interfaces, pas des implémentations concrètes.

**Limite / risque** : si une classe accumule trop de dépendances dans son
constructeur, c'est un signal qu'elle a trop de responsabilités (viole
SRP — voir plus bas) et devrait être scindée.

**Extrait représentatif** (`src/Service/CreerReservationService.php`) :

```php
public function __construct(
    private readonly SalleRepositoryInterface $salles,
    private readonly ReservationRepositoryInterface $reservations,
) {}
```

---

## 10. Conteneur d'injection (PHP-DI)

**Classes concernées** : `config/container.php`, `public/index.php`.

**Rôle** : construire automatiquement le graphe d'objets de
l'application (résoudre `CreerReservationService` implique de résoudre
`SalleRepositoryInterface`, ce qui implique de résoudre
`EloquentSalleRepository`, etc.), en respectant les définitions fournies.

**Avantage** : on écrit `new` une seule fois, quelque part dans le
conteneur ou jamais (autowiring), au lieu de construire manuellement des
dizaines d'objets dans `public/index.php`.

**Limite / risque** : un conteneur mal utilisé peut se transformer en
« Service Locator » anti-pattern si les classes vont y piocher leurs
propres dépendances (`$container->get(...)` dispersé partout). C'est
explicitement interdit dans ce projet (voir plus bas).

**Extrait représentatif** (`config/container.php`) :

```php
SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
ViewRenderer::class => factory(fn () => new ViewRenderer(dirname(__DIR__) . '/templates')),
```

---

## 11. Autowiring

**Classes concernées** : toutes les classes NON listées explicitement
dans `config/container.php` (`SalleValidator`, `ReservationValidator`,
`CreerReservationService`, `AnnulerReservationService`,
`SalleController`, `ReservationController`, `App\Application`).

**Rôle** : PHP-DI lit, via la réflexion PHP, les types des paramètres du
constructeur d'une classe, et construit automatiquement les arguments
correspondants — sans qu'on ait besoin d'écrire une définition pour
chacune.

**Avantage** : zéro configuration pour la majorité des classes du
projet ; seules les interfaces et les objets nécessitant une
configuration (chaîne de connexion, chemin de dossier) ont besoin d'une
définition explicite.

**Limite / risque** : l'autowiring ne peut PAS deviner quelle
implémentation choisir pour une interface (d'où les définitions
`autowire(EloquentSalleRepository::class)`), ni fournir une valeur
scalaire (chaîne, entier) qu'aucun type ne peut désambiguïser (d'où la
`factory()` pour `ViewRenderer`, qui a besoin d'un chemin de dossier).

**Extrait représentatif** (`src/Controller/ReservationController.php`,
construit entièrement par autowiring — sa signature suffit) :

```php
public function __construct(
    private readonly ReservationRepositoryInterface $reservations,
    private readonly SalleRepositoryInterface $salles,
    private readonly ReservationValidator $validator,
    private readonly CreerReservationService $creerReservation,
    private readonly AnnulerReservationService $annulerReservation,
    private readonly ViewRenderer $view,
) {}
```

---

## 12. Inversion de contrôle (IoC)

**Classes concernées** : l'ensemble de l'architecture ; illustrée par
`SalleRepositoryInterface` / `EloquentSalleRepository`.

**Rôle** : au lieu qu'une classe de haut niveau (`CreerReservationService`)
contrôle directement la création de ses dépendances de bas niveau
(`new EloquentSalleRepository()`), c'est un composant externe (le
conteneur) qui « inverse » ce contrôle et fournit la dépendance depuis
l'extérieur, déjà construite.

**Avantage** : le Service dépend d'une abstraction
(`SalleRepositoryInterface`) qu'il ne contrôle pas, ce qui rend son
comportement prévisible et son remplacement (mémoire, Eloquent, ou plus
tard une API externe) transparent pour lui.

**Limite / risque** : l'IoC est un principe ; l'injection de dépendances
et le conteneur en sont des *mécanismes*. Confondre les deux peut faire
croire, à tort, qu'un conteneur d'injection est obligatoire pour faire de
l'IoC — un simple passage d'argument au constructeur suffit déjà.

**Extrait représentatif** : le Service ne fait *jamais* `new EloquentSalleRepository()` lui-même ; c'est le conteneur qui le décide,
via la ligne `autowire(EloquentSalleRepository::class)`.

---

## 13. Les cinq principes SOLID

**S — Single Responsibility Principle** (responsabilité unique)
Chaque classe a une seule raison de changer : `ReservationValidator` ne
change que si le format des données change ; `CreerReservationService` ne
change que si une règle métier change ; `EloquentReservationRepository`
ne change que si la façon d'accéder aux données change.
*Avantage* : classes petites, faciles à tester isolément.
*Risque* : un découpage trop fin multiplie les classes et peut nuire à la
lisibilité globale si mal documenté.

**O — Open/Closed Principle** (ouvert/fermé)
`CreerReservationService` est fermé à la modification pour changer de
source de données, mais ouvert à l'extension : il suffit de fournir une
nouvelle implémentation de `ReservationRepositoryInterface` (par exemple
une version mise en cache) sans toucher au Service.
*Avantage* : on ajoute des comportements sans risquer de casser
l'existant.
*Risque* : anticiper trop d'extensibilité (interfaces pour tout, même ce
qui ne changera jamais) complique inutilement le code — ici, on ne l'a
fait que pour Repository et Validator, pas pour les DTO ou les
Exceptions.

**L — Liskov Substitution Principle** (substitution de Liskov)
`InMemoryReservationRepository` (utilisée dans les tests) est
substituable à `EloquentReservationRepository` partout où
`ReservationRepositoryInterface` est attendu : `CreerReservationService`
se comporte de façon identique et cohérente avec les deux.
*Avantage* : c'est ce qui permet de tester les règles métier sans base de
données réelle.
*Risque* : si une implémentation trichait (par exemple, si la doublure en
mémoire « oubliait » de vérifier le statut `confirmée` dans
`rechercherConflits()`), les tests passeraient pour de mauvaises raisons.
C'est pourquoi la doublure reproduit fidèlement la même logique de
filtrage que l'implémentation Eloquent.

**I — Interface Segregation Principle** (ségrégation des interfaces)
`SalleRepositoryInterface` et `ReservationRepositoryInterface` sont deux
interfaces distinctes et volontairement minimalistes (3 à 6 méthodes
chacune), plutôt qu'une seule grosse interface `RepositoryInterface`
générique. `ValidatorInterface` n'expose qu'une seule méthode
(`validate()`).
*Avantage* : une classe qui implémente `SalleRepositoryInterface` n'est
jamais forcée d'implémenter des méthodes de réservation qui ne la
concernent pas.
*Risque* : trop de petites interfaces très spécifiques peut disperser un
même concept (« accès aux données ») dans de nombreux contrats à
maintenir en parallèle.

**D — Dependency Inversion Principle** (inversion des dépendances)
`CreerReservationService` (haut niveau, règle métier) dépend de
`SalleRepositoryInterface` (abstraction), jamais de
`EloquentSalleRepository` (bas niveau, détail technique). C'est
l'abstraction qui appartient au domaine métier (`src/Repository/*Interface.php`),
pas à l'implémentation.
*Avantage* : le domaine métier ne connaît rien de MySQL, d'Eloquent ni du
SQL — il resterait valide même si l'application changeait totalement de
moteur de stockage.
*Risque* : à ne pas confondre avec l'injection de dépendances (mécanisme)
ni avec le conteneur (outil) : le principe, c'est la direction de la
dépendance (le concret dépend de l'abstrait, jamais l'inverse).

---

## Anti-pattern explicitement évité : le Service Locator

Le sujet interdit explicitement d'injecter `ContainerInterface` dans une
classe métier pour qu'elle aille elle-même y chercher ses dépendances :

```php
// À ÉVITER (anti-pattern Service Locator)
final class CreerReservationService
{
    public function __construct(private ContainerInterface $container) {}
    public function executer(...) {
        $salles = $this->container->get(SalleRepositoryInterface::class); // dépendance cachée
    }
}
```

Ce projet respecte la règle : **seuls `public/index.php` et
`App\Application::traiter()`** appellent `$container->get(...)`
directement (le premier pour démarrer Eloquent et récupérer
`Application`, le second parce que FastRoute ne lui a donné qu'un nom de
classe à résoudre). Toutes les autres classes reçoivent leurs dépendances
par le constructeur, ce qui les rend honnêtes (leurs besoins sont visibles
dans leur signature) et testables sans conteneur.
