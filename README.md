# HireFlow

Plateforme SaaS de recrutement conçue comme un **monolithe modulaire**
reposant sur Domain-Driven Design et une architecture hexagonale.

Le projet explore la conception d'une application métier complexe :
gestion des candidats, entreprises, recruteurs, offres d'emploi,
candidatures et workflows de recrutement.

## Objectifs techniques

- Concevoir un domaine métier découplé du framework
- Structurer l'application en bounded contexts
- Isoler Domain, Application et Infrastructure
- Construire une API REST avec Symfony et API Platform
- Développer un frontend candidat avec Next.js
- Documenter les décisions via des ADR
- Préparer l'application à évoluer sans basculer prématurément vers des microservices

## Stack

| Domaine | Technologie |
|---|---|
| Backend | Symfony 8 |
| API | API Platform |
| ORM | Doctrine |
| Database | PostgreSQL |
| Cache / Lock | Valkey |
| Search | Meilisearch |
| Frontend | Next.js / TypeScript |
| Reverse Proxy | Traefik |
| Containers | Docker |
| Architecture | DDD / Hexagonal / Modular Monolith |

## Architecture

```text
backend/
├── src/
│   ├── Shared/
│   └── IdentityAccess/
│       ├── Domain/
│       ├── Application/
│       └── Infrastructure/
├── tests/
└── ...

frontend/
├── app/
├── components/
└── ...

docs/
└── adr/
```

## Engineering topics

Le projet me permet notamment de travailler sur :

- Domain-Driven Design
- Bounded Contexts
- Value Objects
- Authorization / RBAC
- Multi-tenant SaaS foundations
- API design
- Search
- Dockerized development
- Architectural Decision Records
- Testing

Statut

## 🚧 En développement.

Les fondations techniques sont opérationnelles et
l'implémentation progresse bounded context par bounded context.

## Documentation

La documentation fonctionnelle et les décisions techniques
sont disponibles dans le repository :

- cahier des charges
- ADR
- documentation d'architecture


**Description GitHub :**

> Recruitment SaaS built as a modular monolith with Symfony, API Platform, Next.js, PostgreSQL and DDD.

Topics :

```text
symfony
api-platform
nextjs
postgresql
ddd
hexagonal-architecture
modular-monolith
docker
saas
```
