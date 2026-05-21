# Solar Score Arena

Aplicacion web dinamica construida con HTML, CSS, JavaScript y PHP.
Consume una API publica del sistema solar para generar un juego de puntajes, registra resultados en base de datos y guarda logs de eventos.

Guia de onboarding tecnico:
- ONBOARDING.md

## Requisitos funcionales cubiertos
1. Usuarios previamente registrados (seed automatico).
2. Insercion del usuario y puntaje en BD al terminar cada partida.
3. Registro de eventos en archivo de log configurable.
4. Puntaje con usuario, fecha y rondas almacenado en BD.
5. Consulta de historial de una persona.
6. Reportes agrupados por semana, mes y todo el tiempo.

## Tecnologias
- Frontend: HTML + CSS + JS (vanilla)
- Backend: PHP 8+
- Base de datos: MySQL (XAMPP / MySQL Workbench)
- API publica consumida: `https://api.le-systeme-solaire.net/rest/bodies/`
- Logging: archivo JSON lines en `logs/solar_events.log`

## Estructura
```text
web_api_system_solar/
  front/
    index.php
    login.php
    dashboard.php
    scripts/app.js
    styles/styles.css
  back/
    bootstrap.php
    conexion.php
    db.php
    helpers.php
    logger.php
    auth_login.php
    logout.php
  api/
    game_data.php
    save_score.php
    report_data.php
  database/
    schema.sql
  logs/
  index.php
  DOCUMENTACION.md
```

## Ejecucion local
1. En XAMPP, iniciar `Apache` y `MySQL`.
2. (Opcional) Ejecutar `database/schema.sql` en Workbench o phpMyAdmin.
3. Verificar credenciales MySQL en `back/conexion.php`.
4. Desde la raiz del proyecto (si no usas Apache de XAMPP):
   - `php -S localhost:8000`
5. Abrir:
   - `http://localhost:8000`

Nota: el backend tambien crea automaticamente la base y tablas si el usuario MySQL tiene permisos.

## Usuarios de prueba
- neo.solar / sol12345
- luna.bit / luna12345
- astro.admin / admin12345
