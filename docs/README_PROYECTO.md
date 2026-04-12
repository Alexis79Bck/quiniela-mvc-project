# Sistema de Quiniela FIFA 2026 - Documentación del Proyecto

## 🎯 Objetivo del Proyecto

Preparar el proyecto Laravel como **base fundacional** para el desarrollo de una aplicación de Quiniela para la **FIFA Copa Mundial de Futbol 2026**. el desarrollo estara a mano de 2 desarrolladores FullStack.

## 📋 Alcance del Sistema

### Concepto del Juego
- **Evento**: FIFA Copa Mundial de Futbol 2026
- **Mecánica**: Pronosticar resultados de cada encuentro
- **Puntuación**: Usuarios acumulan puntos según criterios de acierto
- **Clasificación**: Sistema Ladder (escalera) para determinar ganadores
- **Etapas**:
  1. Fase de grupos/clasificación
  2. Emparejamientos/brackets (16vos hasta la final)
- **Ganadores**: Top 2-3 por etapa
- **Audiencia**: Grupo limitado de personas (familiar/amigos)

### Características Principales
- ✅ Autenticación robusta (Sanctum + Fortify)
- ✅ Control de acceso granular (Spatie Permission)
- ✅ Sistema de predicciones
- ✅ Motor de puntuación automática
- ✅ Tabla de Clasificación (Leaderboard)
- ✅ Gestion de Notificaciones Toast enfocado en API Restful
- ✅ Auditoría completa de acciones
- ✅ Arquitectura MVC standard escalable


## 🏗️ Arquitectura del Sistema

### Estructura MVC Standard (Estructura Laravel MVC por defecto)

### Paquetes Implementados
- **Laravel Sanctum**: Autenticación API y SPA
- **Laravel Fortify**: Autenticación headless
- **Spatie Laravel Permission**: Roles y permisos
- **Spatie Laravel Activitylog**: Gestion de REgistros de Actividades para Auditoria

## 📅 Plan de Implementación

### Fases del Proyecto
| Fase | Duración | Descripción |
|------|----------|-------------|
| **Fase 1** | 2 días | Configuración base y autenticación |
| **Fase 2** | 1 día | Logging y auditoría |
| **Fase 3** | 1 día | Notificaciones Toast para  |
| **Fase 4** | 3 días | Estructura DDD y dominio de Quiniela |
| **Fase 5** | 3 días | Lógica de negocio |
| **Fase 6** | 3 días | API y controladores |
| **Fase 7** | Indeterminado | Frontend y vistas (Aun no definido que tecnologia implementar. Tentativamente se encuentra Livewire 3 y Vue 3) |
| **Fase 8** | 2 días | Pruebas y documentación |

**Total estimado**: Indeterminado por definicion de frontend.

## 🎮 Sistema de Puntuación

### Criterios de Puntuación
| Criterio | Puntos | Descripción |
|----------|--------|-------------|
| Marcador exacto | 5 | Acertar marcador exacto |
| Ganador correcto | 3 | Acertar equipo ganador |
| Goles de equipo | 1 | Acertar goles de un equipo. Excluye el marcador Exacto |
| Bono: Acierto Total de Goles | 2 | Acertar el numero Total de goles. Opcional solo si el Jugador lo habilita para el evento. |

### Ejemplo
**Partido**: Brasil 2 - 1 Argentina

| Predicción | Puntos | Razón |
|------------|--------|-------|
| Brasil 2 - 1 Argentina | 5 | Resultado exacto |
| Brasil 3 - 1 Argentina | 3 | Ganador correcto |
| Brasil 2 - 0 Argentina | 1 | Acierto de Gol de un Equipo |
| Brasil 3 - 0 Argentina | 2 | BONO: Si esta habilitado, Acierto de total de goles |

## 👥 Roles y Permisos

### Roles del Sistema
1. **SuperAdmin**: Acceso total al sistema
2. **Administrador**: Gestión de quinielas y partidos
3. **Jugador**: Participación en quinielas

### Permisos Granulares
- `manage-users`: Gestionar usuarios
- `manage-quinielas`: Gestionar quinielas
- `manage-matches`: Gestionar partidos
- `manage-teams`: Gestionar equipos
- `make-predictions`: Realizar predicciones
- `view-results`: Ver resultados
- `view-leaderboard`: Ver clasificación
- `view-audit-logs`: Ver logs de auditoría

## 🔔 Sistema de Notificaciones Toast

### Eventos Notificables
- Nueva quiniela disponible
- Inicio de partido
- Finalización de partido
- Resultado Final de partido
- Movimiento de posición en la Tabla de Clasificación
- Actualización General de la Tabla de Clasificación
- Recordatorio de predicción. Countdown hasta 5 minutos antes del inicio del partido
- Notificación de ganadores

### Canales de Notificación
- **Database**: Persistente,

## 🔒 Seguridad

### Medidas Implementadas
- Tokens de API con expiración
- Rate limiting en endpoints
- Protección CSRF
- Roles y permisos granulares
- Auditoría de acciones críticas
- Encriptación de datos sensibles

## 📊 Monitoreo y Auditoría

### Canales de Logging
- `audit`: Acciones críticas (90 días)
- `security`: Intentos de acceso (180 días)
- `prediction`: Predicciones realizadas (60 días)
- `scoring`: Cálculos de puntuación (60 días)

### Eventos Auditados
- Login/logout de usuarios
- Creación/modificación de quinielas
- Realización de predicciones
- Cambios en puntuaciones
- Acciones administrativas
- Errores del sistema

## 📝 Notas Importantes

### Consideraciones
- **Respaldar base de datos** antes de migraciones
- **Probar en entorno local** antes de producción
- **Revisar logs** después de cada fase
- **Validar permisos** después de configurar roles
- **Verificar notificaciones** en tiempo real

### Mejores Prácticas
- Seguir estándares PSR-12
- Escribir código limpio y documentado
- Realizar code reviews
- Ejecutar pruebas continuamente
- Mantener documentación actualizada

## 🎉 Conclusión

Este proyecto proporciona una **base sólida y profesional** para el desarrollo de una aplicación de quiniela para el Mundial 2026. La arquitectura DDD garantiza escalabilidad y mantenibilidad, mientras que los paquetes implementados proporcionan funcionalidades robustas de autenticación, autorización y notificaciones.

El equipo de desarrollo está equipado con toda la documentación necesaria para comenzar la implementación de manera efectiva y eficiente.

---

**Proyecto**: Sistema de Quiniela FIFA 2026
**Versión**: 1.0
**Fecha**: 27 de Marzo de 2026
**Estado**: Listo para implementación
**Equipo**: 2 desarrolladores fullstack
