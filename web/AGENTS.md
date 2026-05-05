# SWAPP - Street Worker App

## Projektübersicht
SWAPP ist eine Webanwendung für Streetworker zur Dokumentation von Rundgängen (Walks), Wegpunkten (WayPoints) und Team-Management.

## Tech Stack

### Backend
- **PHP 8.4+** mit **Symfony 6.4**
- **API Platform 4.0** für REST API
- **Doctrine ORM 3.3** mit MySQL/MariaDB
- **Lexik JWT** für Authentifizierung

### Frontend
- **Vue 3** (Composition API bevorzugt)
- **TypeScript** (streng typisiert)
- **Vuetify 3.x** als UI Framework
- **Vite 6** als Build-Tool
- **Pinia** für State Management
- **Leaflet** für Karten-Funktionalität

### Testing
- **Behat** für Acceptance & Integration Tests
- **PHPUnit** für Unit Tests
- **PHPStan** Level max für statische Analyse
- **PHPCS** mit projektmotor/symfony-coding-standard

## Projektstruktur

```
web/
├── src/
│   ├── Controller/        # Symfony Controller (selten, meist API Platform)
│   ├── Entity/            # Doctrine Entitäten (ORM Models)
│   ├── Dto/               # Data Transfer Objects für API
│   ├── Handler/           # Command/Request Handler (CQRS-like)
│   ├── Repository/        # Doctrine Repositories
│   ├── Security/Voter/    # Symfony Voter für Authorisierung
│   ├── DataProvider/      # API Platform Custom Data Provider
│   ├── DataTransformer/   # API Platform Input/Output Transformer
│   ├── Validator/         # Custom Symfony Validators
│   └── Migrations/        # Doctrine Migrations
├── assets/js/
│   ├── components/        # Vue 3 SFC Komponenten
│   ├── stores/            # Pinia Stores
│   ├── models/            # TypeScript API Models (auto-generiert)
│   ├── api/               # API Client Setup
│   └── app.ts             # Vue App Entry Point
├── tests/
│   ├── Context/           # Behat Contexts
│   └── Integration/features/  # Behat Feature Files
├── config/                # Symfony Konfiguration
└── templates/             # Twig Templates (minimal, SPA)
```

## Wichtige Befehle

### Development
```bash
# Backend
composer install
bin/console doctrine:migrations:migrate

# Frontend
yarn install
yarn dev              # Vite Dev Server
yarn build            # Production Build
yarn tsc              # TypeScript Check

# Tests ausführen
composer behat        # Alle Behat Tests
composer pbehat       # Parallel Behat (16 Prozesse)
composer unit         # PHPUnit
composer qa           # Alle QA Checks (CS, PHPStan, Tests)
```

### Code Quality
```bash
composer cs           # PHPCS Check
composer cbf          # PHPCS Auto-Fix
composer stan         # PHPStan Analyse
composer security     # Security Check
```

### Datenbank
```bash
# Test DB neu aufsetzen
composer compile-test

# Dev DB neu aufsetzen
composer database-init

# Neue Migration erstellen
bin/console make:migration
```

### API Client generieren
```bash
composer create-api-client  # Generiert TypeScript Models aus OpenAPI
```

## Coding Standards

### PHP
- PSR-12 Coding Standard
- PHPStan Level max (strictes Type-Checking)
- Doctrine Annotations bevorzugt über Attributes
- **CQRS-Pattern**: API Requests → Dto → Handler → Entity
- Voter für alle Authorisierungsprüfungen
- Repository-Interface + Doctrine Implementation

### TypeScript/Vue
- **Composition API** (nicht Options API)
- Strict TypeScript (`noImplicit*: true`)
- Vue 3 `<script setup lang="ts">` Syntax
- Komponenten in PascalCase
- Props und Emits explizit typisieren
- Pinia für globalen State, nicht Props-Drilling

### Testing
- Behat für alle User-Features (BDD)
- Feature Files in Deutsch
- PHPUnit für komplexe Business-Logik
- Mindestens Integration-Test-Coverage für API Endpoints

## Domain-Modelle (Kernentitäten)

- **User**: Nutzer mit Rollen (Admin, User)
- **Team**: Organisation/Team
- **Walk**: Rundgang mit Datum, Team, WayPoints
- **WayPoint**: Einzelner Wegpunkt mit GPS, Notizen, Bildern
- **Client**: Betreute Person (anonymisiert)
- **Tag**: Kategorisierung von WayPoints
- **UserPreferences**: Nutzer-Einstellungen

## Häufige Aufgaben

### Neues API-Feature hinzufügen
1. Dto in `src/Dto/` erstellen (Request/Response)
2. Handler in `src/Handler/` implementieren
3. Entity um Felder erweitern (falls nötig)
4. Voter für Authorisierung erstellen/anpassen
5. Behat-Feature schreiben (`tests/Integration/features/`)
6. Migration erstellen: `bin/console make:migration`
7. API-Client neu generieren: `composer create-api-client`
8. Vue-Komponente anpassen/erstellen

### Vue-Komponente erstellen
1. Komponente in `assets/js/components/` anlegen
2. `<script setup lang="ts">` verwenden
3. Props mit `defineProps<{ ... }>()` typisieren
4. Vuetify-Komponenten verwenden (`<v-btn>`, `<v-card>`, etc.)
5. API-Calls über generierten Client in `assets/js/models/`

## Besonderheiten

- **Vite + Symfony Integration** über `pentatrion/vite-bundle`
- **OpenAPI-Schema** automatisch generiert → TypeScript Client
- **JWT Refresh Token** Mechanismus aktiv
- **Leaflet Maps** für WayPoint-Visualisierung
- **PWA Support** mit Service Worker (vite-plugin-pwa)
- **Parallel Behat Tests** mit 16 Prozessen für schnelles Feedback

## Branches
- **Main**: `develop`

## Git Workflow
- Feature Branches von `develop` abzweigen
- PRs gegen `develop` erstellen
- Behat Tests müssen grün sein vor Merge
