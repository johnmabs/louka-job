# HireFlow

Plateforme SaaS de recrutement — Modular Monolith DDD/Hexagonal (Symfony 8 + API Platform) avec
portail public/candidat en Next.js 16 et back-office recruteur en Symfony UX.

Cahier des charges complet : [`HireFlow-Cahier-des-Charges.md`](./HireFlow-Cahier-des-Charges.md).
Décisions techniques tracées dans [`docs/adr/`](./docs/adr/).

---

## Stack

| Composant                    | Techno                               | Version                                                  |
| ---------------------------- | ------------------------------------ | -------------------------------------------------------- |
| Backend / API                | Symfony                              | 8                                                        |
| ORM                          | Doctrine                             | —                                                        |
| API                          | API Platform                         | (Resources séparées des entités Doctrine)                |
| Base de données              | PostgreSQL                           | 18                                                       |
| Cache / Lock / Rate limiting | Valkey                               | 8 (voir [ADR-0002](./docs/adr/0002-valkey-vs-redis.md))  |
| Recherche                    | Meilisearch                          | v1.50                                                    |
| Reverse proxy                | Traefik                              | v3.7                                                     |
| Frontend public/candidat     | Next.js (App Router, TS, Tailwind)   | 16 (voir [ADR-0001](./docs/adr/0001-nextjs16-node24.md)) |
| Runtime frontend             | Node.js                              | 24                                                       |
| Back-office recruteur/admin  | Symfony UX (Twig + Stimulus + Turbo) | — (à venir)                                              |
| Mail (dev)                   | Mailpit                              | —                                                        |

## Prérequis

- Docker Desktop
- Une entrée dans `/etc/hosts` (ou équivalent Windows) : 127.0.0.1 hireflow.local api.hireflow.local

## Démarrage

```bash
git clone <repo>
cd hireflow
docker compose up -d --build
```

| Service                      | URL                           |
| ---------------------------- | ----------------------------- |
| Frontend (Next.js)           | http://hireflow.local         |
| API (Symfony / API Platform) | http://api.hireflow.local/api |
| Mailpit (emails de dev)      | http://localhost:8025         |

## Commandes utiles

```bash
# Composer / artisan-like côté backend
docker compose run --rm php composer <commande>
docker compose run --rm php bin/console <commande>

# npm côté frontend
docker compose run --rm nextjs npm <commande>

# Logs d'un service
docker compose logs -f <service>
```

## Structure du repo

```
hireflow/
├── backend/ # Symfony 8 (API + futur back-office Symfony UX)
│ └── src/
│ ├── Shared/ # Shared Kernel : Value Objects génériques
│ │ # réutilisables entre Bounded Contexts
│ │ # (Uuid, Email...). Ne contient jamais de
│ │ # concept métier propre à un seul contexte.
│ └── IdentityAccess/ # Bounded Context : comptes, auth, RBAC
│ ├── Domain/ # Métier pur, zéro dépendance framework
│ ├── Application/ # Commands/Queries, orchestration
│ └── Infrastructure/ # Doctrine, HTTP, API Platform
├── frontend/ # Next.js 16 (portail public + espace candidat)
├── docker/ # Dockerfiles (php, nextjs) + config nginx
├── docs/adr/ # Architecture Decision Records
├── compose.yaml
└── HireFlow-Cahier-des-Charges.md
```

### Convention : Shared Kernel

Un Value Object va dans `src/Shared/Domain/` seulement s'il est **techniquement générique**
(pas de règle métier propre à un seul contexte) : `Uuid`, `Email`, plus tard `Money`, `Address`...

Les **identités d'agrégat** (`UserId`, futur `CompanyId`, `JobPostingId`...) restent **locales**
à leur Bounded Context, même si elles composent en interne le `Uuid` partagé — un autre contexte
ne doit jamais importer directement le type d'identité d'un autre contexte (ça créerait un
couplage contraire au principe des Bounded Contexts, cahier des charges §3.1.3).

---

## Journal d'avancement

### Sprint 0 — Fondations ✅ (2026-08-03)

- Structure du repo, `.gitignore`, `compose.yaml`.
- Dockerfile PHP 8.4-fpm (extensions `pdo_pgsql`, `redis`, `intl`, `apcu`, `opcache`).
- Dockerfile Next.js 16 (Node 24).
- Stack Docker complète : Traefik, Postgres 18, Valkey 8, Meilisearch 1.50, Mailpit.
- Symfony 8 + API Platform bootstrapés dans `backend/`, routing Traefik → Nginx → PHP-FPM validé.
- Next.js 16 + Tailwind bootstrapé dans `frontend/`, routing Traefik → Next.js validé.
- ADR 0001, 0002, 0003 rédigés (versions Next.js/Node, Valkey, versions infra).

### Sprint 1 — Module Identity & Access ✅ (essentiel terminé, MFA reporté)

- [x] Structure du module (`Domain` / `Application` / `Infrastructure`)
- [x] Shared Kernel (`Uuid`, `Email`) + identité locale `UserId`
- [x] Entité de domaine `User` (agrégat pur, `roles` minimal `ROLE_CANDIDATE`)
- [x] Mapping Doctrine XML + Custom Types (`UserIdType`, `EmailType`)
- [x] Repository (Port/Adapter Doctrine)
- [x] Inscription + hash Argon2id
- [x] Vérification d'email (token HMAC, 24h, usage unique) + envoi réel (Mailer → Mailpit)
- [x] Connexion + JWT RS256 (POST /api/v1/auth/login)
- [x] Refresh token rotatif dans Valkey (POST /api/v1/auth/refresh) — rotation + détection de rejeu par chaîne
- [x] Inscription, vérification d'email, connexion JWT, refresh token rotatif, RBAC
- [ ] MFA/TOTP — reporté, à reprendre plus tard

### Sprint 2 — Module Company ✅ (essentiel terminé)

- [x] Agrégat Domain Company + CompanyMember (RBAC organisation owner/admin/recruiter/viewer)
- [x] Création d'entreprise (POST /api/v1/companies) — créateur devient owner automatiquement
- [x] Création de recruteur par owner/admin (POST /api/v1/companies/{id}/recruiters) —
      orchestration cross-module avec IdentityAccess
- [ ] Retrait / changement de rôle d'un membre
- [ ] Vérification SIRET (asynchrone)
- [ ] Voter Symfony pour les permissions au niveau entreprise (actuellement vérifié
      uniquement en Domain, pas encore de retour HTTP 403 "propre" avant appel Handler)

---

## Conventions de code

### Emplacement des enums dans un module Domain

- `Domain/Model/` : enums représentant l'état propre d'une entité/agrégat (ex. `UserStatus`
  pour `User`, `VerificationStatus` pour `Company`, `CompanyRole` pour `CompanyMember`).
- `Domain/ValueObject/` : enums qui sont un composant interne d'un Value Object, sans exister
  de façon autonome (ex. `AvailabilityStatus` n'a de sens que comme discriminant d'`Availability`,
  `RemotePreference` que comme champ de `Location`).

---

_Ce README est mis à jour à chaque fin de sprint — voir aussi les ADR pour le détail des décisions
qui s'écartent du cahier des charges initial._
