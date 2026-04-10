# Arquitectura del Sistema

## Patrones de Diseño

### MVC Standard

```
Request → Controller → Service → Repository → Model → Database
         ↳ Response ← Resource ←
```

### Service Layer

Encapsula la lógica de negocio:

```php
// app/Services/PredictionService.php
class PredictionService
{
    public function createPrediction(int $userId, array $data): Prediction;
    public function calculatePoints(int $predictionId): int;
    public function getLeaderboard(): Collection;
}
```

### Repository Pattern

Abstrae el acceso a datos:

```php
// app/Repositories/PredictionRepository.php
interface PredictionRepository
{
    public function findByUser(int $userId): Collection;
    public function findByMatch(int $matchId): Collection;
}
```

### Strategy Pattern

Permite intercambio de algoritmos:

```php
// app/Strategies/Scoring/ScoreStrategy.php
interface ScoreStrategy
{
    public function calculate(Prediction $prediction, Match $match): int;
}

// Estrategias concretas
class ExactScoreStrategy implements ScoreStrategy { }
class ResultOnlyStrategy implements ScoreStrategy { }
class BonusPointsStrategy implements ScoreStrategy { }
```

### Factory Pattern

Crea objetos complejos:

```php
// app/Factories/TournamentFactory.php
class TournamentFactory
{
    public static function createGroupStage(int $teamCount): Stage;
    public static function createEliminationStage(int $teamCount): Stage;
}
```

## Flujo de Datos

1. Usuario autenticado envía predicción via API
2. Controller valida request
3. Service aplica reglas de negocio
4. Repository persiste datos
5. Response con resultado

## Estructura de Carpetas

```
app/
├── Http/Controllers/Api/
│   └── PredictionController.php
├── Services/
│   └── PredictionService.php
├── Repositories/
│   └── EloquentPredictionRepository.php
├── Strategies/Scoring/
│   ├── ScoreStrategy.php
│   ├── ExactScoreStrategy.php
│   └── ResultOnlyStrategy.php
├── Factories/
│   ├── MatchFactory.php
│   └── TournamentFactory.php
└── Models/
```