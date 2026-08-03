# ADR-0003 : Versions d'infrastructure mises à jour par rapport au cahier des charges

**Statut :** Accepté
**Date :** 2026-08-03

## Contexte

L'exemple `docker-compose.yml` du cahier des charges (§28.3) fige des versions précises
(Traefik v3.1, PostgreSQL 17, Meilisearch v1.10) qui reflètent l'état de l'art au moment de la
rédaction. Le Sprint 0 a démarré après la sortie de versions plus récentes de ces composants.

## Décision

On démarre directement sur les versions suivantes, plus récentes que celles du document initial :

| Composant   | Version cahier des charges | Version retenue |
| ----------- | -------------------------- | --------------- |
| Traefik     | v3.1                       | v3.7            |
| PostgreSQL  | 17                         | 18              |
| Meilisearch | v1.10                      | v1.50           |

Point notable : PostgreSQL 18 introduit une fonction native `uuidv7()`, directement utile pour
respecter l'exigence du cahier des charges (§3.3) d'utiliser des UUID v7 comme identifiants
primaires externes, sans dépendre d'une bibliothèque PHP tierce pour la génération.

Changement de comportement à noter : à partir de PostgreSQL 18, l'image Docker officielle attend
un point de montage unique à `/var/lib/postgresql` (et non plus `/var/lib/postgresql/data`), pour
un format de données compatible `pg_ctlcluster`.

## Alternatives considérées

- **Suivre les versions exactes du cahier des charges** : rejeté — reviendrait à démarrer un
  projet neuf sur des versions déjà dépassées de plusieurs versions mineures/majeures.

## Conséquences

- Bénéfice direct pour le modèle de données (`uuidv7()` natif en PostgreSQL 18).
- Le point de montage du volume Postgres diffère de l'exemple du cahier des charges — à garder en
  tête si un membre de l'équipe copie l'extrait §28.3 tel quel depuis le document.
- Aucune fonctionnalité requise par le cahier des charges n'est perdue ou rendue incompatible par
  ces montées de version.
