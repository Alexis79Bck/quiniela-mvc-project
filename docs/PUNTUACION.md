# Sistema de Puntuación

## Puntos por Acierto

### General

| Acierto | Puntos |
|---------|--------|
| Ganador/Empate + Marcador Exacto (marcador exacto) | 5 puntos |
| Ganador/Empate | 3 puntos |
| Adicional: Acierto en Goles de Equipos (Excepto Marcador Exacto) | 1 puntos |
| Incorrecto | 0 puntos |

### Ejemplos

```
Partido: Argentina 2 - 1 Brasil

Predicción: Argentina 2 - 1 Brasil → 10 puntos (exacto)
Predicción: Argentina 3 - 1 Brasil → 5 puntos (ganador correcto)
Predicción: Argentina 1 - 1 Brasil → 1 punto (Acierto en Goles Brasil 1)
Predicción: Brasil 2 - 1 Argentina → 0 puntos (incorrecto)
```

## Clasificación (Leaderboard)

### Tipos de Clasificación

1. **General**: Todos los puntos acumulados
2. **Por Etapa**: Puntos de la fase actual (grupos, 16avos, etc.)
3. **Por Jornada**: Puntos de cada fecha

### Visualización

- Posición actual del usuario
- Puntuación total
- Número de aciertos (totales)
- Comparación con top 3

## Cálculo Automático

```php
class ScoringService
{
    public function calculate(Prediction $prediction, Match $match): int
    {
        if ($match->status !== 'finished') {
            return 0;
        }

        $isElimination = $match->stage->order >= 10;
        $score = $isElimination ? 15 : 10;

        // Marcador exacto
        if ($prediction->home_score === $match->home_score &&
            $prediction->away_score === $match->away_score) {
            return $score;
        }

        // Ganador correcto
        $predWinner = $this->getWinner($prediction);
        $matchWinner = $this->getWinner($match);

        if ($predWinner === $matchWinner) {
            return $score / 2;
        }

        return 0;
    }
}
```

## Jorndas y Fechas

### Fase de Grupos

- 5 jornadas por grupo
- 8 grupos = 40 fechas
- Partido por día (configurable)

### Ejemplo de Clasificación por Jornada

```
Jornada 1 - Grupo A:

1. Juan: 3 predicciones correctas (15 pts)
2. María: 2 predicciones correctas (10 pts)
3. Pedro: 1 predicción correcta (5 pts)
```

## Límites

- **Predicciones**: Ilimitadas (una por partido)
- **Edición**: Hasta 1 hora antes del partido
- **Puntuación máxima teórica**:
  - Grupos: 48 × 10 = 480 puntos
  - Eliminación: 31 × 15 = 465 puntos
  - Total: 945 puntos
