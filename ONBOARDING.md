# Onboarding de desarrollo

## Checklist de migracion (otro computador)
1. Instalar/iniciar XAMPP (Apache + MySQL) y copiar el proyecto en htdocs.
2. Ajustar en back/conexion.php: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD y APP_TIMEZONE.
3. Generar y pegar SOLAR_API_KEY en back/conexion.php.
4. Asegurar permisos de logs si no registra eventos:
  - chmod 777 logs
  - touch logs/solar_events.log
  - chmod 666 logs/solar_events.log
5. Probar flujo completo: login, iniciar partida (api/game_data.php), guardar puntaje (api/save_score.php) y validar reportes (api/report_data.php).

## 1) Objetivo funcional del proyecto
Solar Score Arena es una aplicación web académica que:
- autentica usuarios precargados,
- consume una API pública del sistema solar para un juego dinámico,
- guarda puntajes por usuario en MySQL,
- genera reportes por usuario, semana, mes y todo el tiempo,
- registra logs de eventos para trazabilidad.

## 2) Stack técnico
- Frontend: PHP (vistas), HTML, CSS, JavaScript vanilla.
- Backend: PHP 8+ con PDO.
- Base de datos: MySQL (XAMPP o MySQL Workbench).
- API externa: https://api.le-systeme-solaire.net/rest/bodies/
- Logging: archivo JSONL en logs/solar_events.log.

## 3) Estructura de carpetas
- front: pantallas (login y dashboard) y assets de UI.
- back: bootstrap, sesión, utilidades, conexión DB y auth.
- api: endpoints internos consumidos por el frontend JS.
- database: script SQL de referencia para crear esquema.
- logs: bitácora de eventos en runtime.

## 4) Primer arranque local
1. Iniciar Apache y MySQL desde XAMPP.
2. Ajustar credenciales en back/conexion.php si tu entorno no usa root sin password.
3. (Opcional) Ejecutar database/schema.sql en Workbench.
4. Abrir la app desde tu host local.
5. Iniciar sesión con un usuario demo:
   - neo.solar / sol12345
   - luna.bit / luna12345
   - astro.admin / admin12345

Nota: si tu usuario MySQL tiene permisos de CREATE DATABASE, el proyecto crea esquema/tablas automáticamente al iniciar.

## 5) Flujo de ejecución (end-to-end)
1. Entrada en index.php y redirección a front/index.php.
2. Si no hay sesión activa, front/login.php.
3. Login POST hacia back/auth_login.php.
4. Con sesión activa, front/dashboard.php renderiza shell de UI.
5. front/scripts/app.js consume:
   - api/game_data.php para preguntas del juego,
   - api/save_score.php para insertar puntaje,
   - api/report_data.php para reportes.
6. back/logger.php registra eventos clave de auth, API y reportes.

## 6) Contratos de endpoints internos
### GET api/game_data.php
- Requiere sesión.
- Devuelve lista de planetas con pistas.

### POST api/save_score.php
- Requiere sesión.
- Payload JSON esperado:
  - score: int >= 0
  - rounds: int > 0
- Inserta una fila en scores.

### GET api/report_data.php?userId=N
- Requiere sesión.
- Devuelve:
  - users,
  - selectedUserId,
  - summary,
  - history,
  - weekly,
  - monthly,
  - allTime.

## 7) Modelo de datos
Tabla users:
- id, username, full_name, password_hash, created_at.

Tabla scores:
- id, user_id, score, rounds, created_at.

Relación:
- users 1:N scores.

## 8) Dónde tocar código según necesidad
- Cambiar colores/UX: front/styles/styles.css
- Cambiar reglas de puntaje: front/scripts/app.js
- Cambiar rondas por partida: back/conexion.php (ROUNDS_PER_GAME)
- Cambiar conexión MySQL: back/conexion.php
- Cambiar formato de log: back/logger.php
- Ajustar reportes SQL: api/report_data.php

## 9) Convenciones del proyecto
- Todas las rutas protegidas llaman require_login().
- Respuestas JSON usan json_response().
- Eventos importantes pasan por app_log().
- Escapado en frontend y plantillas para evitar inyección HTML.

## 10) Troubleshooting rápido
- Error de conexión DB:
  - validar DB_HOST, DB_PORT, DB_USER, DB_PASSWORD en back/conexion.php.
- Login siempre inválido:
  - verificar que existan usuarios en tabla users.
- Reportes vacíos:
  - revisar inserciones en scores y logs en logs/solar_events.log.
- API pública no responde o sale fallback:
  - validar SOLAR_API_KEY en back/conexion.php.
  - probar token manualmente con:
    - curl -i -H "Authorization: Bearer TU_TOKEN" https://api.le-systeme-solaire.net/rest/bodies/
- Logs no se actualizan en otro computador (Permission denied):
  - chmod 777 logs
  - touch logs/solar_events.log
  - chmod 666 logs/solar_events.log

## 11) Checklist para cambios futuros
1. Implementar cambio.
2. Registrar evento en log si afecta flujo clave.
3. Actualizar documentación (README, DOCUMENTACION u ONBOARDING).
4. Validar login, juego, guardado y reportes.
5. Confirmar que no se rompen rutas protegidas.
