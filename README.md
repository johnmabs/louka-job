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

### Sprint 1 — Module Identity & Access 🚧 (en cours)

- [x] Structure du module (`Domain` / `Application` / `Infrastructure`)
- [x] Shared Kernel (`Uuid`, `Email`) + identité locale `UserId`
- [x] Entité de domaine `User` (agrégat pur, factories `register()` / `reconstitute()`)
- [x] Mapping Doctrine XML + Custom Types (`UserIdType`, `EmailType`)
- [x] Migration + table `identity_access_user`
- [x] Repository (`UserRepositoryInterface` + implémentation Doctrine)
- [x] Inscription + hash Argon2id (Application + API Platform)
- [x] Vérification d'email (token HMAC signé, 24h, usage unique)
- [x] Envoi réel de l'email de vérification (Mailer/Notifier → Mailpit)
- [ ] Connexion + JWT RS256 (access token + refresh token rotatif)
- [ ] RBAC hiérarchique (`ROLE_CANDIDATE`, `ROLE_RECRUITER`, `ROLE_COMPANY_ADMIN`, `ROLE_PLATFORM_ADMIN`)
- [ ] MFA/TOTP

---

_Ce README est mis à jour à chaque fin de sprint — voir aussi les ADR pour le détail des décisions
qui s'écartent du cahier des charges initial._
