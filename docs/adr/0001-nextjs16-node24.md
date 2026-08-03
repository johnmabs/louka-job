# ADR-0001 : Next.js 16 (LTS) et Node.js 24 (Active LTS) au lieu de Next.js 15 / Node 20

**Statut :** Accepté
**Date :** 2026-08-03

## Contexte

Le cahier des charges v1.0 (§4.2, §4.3) spécifie Next.js 15 pour le portail public et l'espace
candidat. Ce choix reflète l'état de l'art au moment de la rédaction du document, mais le
développement démarre après la sortie de Next.js 16.

Au moment du Sprint 0 :

- Next.js 16 est sorti le 21 octobre 2025 et constitue la version LTS activement supportée.
- Next.js 15 est en maintenance, fin de support prévue le 21 octobre 2026 — soit quelques mois
  après le démarrage du projet.
- Node.js 20, initialement envisagé comme runtime, est déjà en End-of-Life depuis le 30 avril 2026.
- Node.js 22 est en Maintenance LTS (fin de support avril 2027).
- Node.js 24 est en Active LTS (fin de support avril 2028).

## Décision

On démarre le projet directement sur **Next.js 16** et **Node.js 24**, plutôt que de suivre à la
lettre les versions du cahier des charges.

## Alternatives considérées

- **Rester sur Next.js 15 / Node 20** : rejeté — programmerait une migration forcée dans les
  mois suivant le lancement du projet, pour suivre à la lettre un document déjà daté sur ce point.
- **Next.js 16 / Node 22** : rejeté — Node 22 est fonctionnellement correct mais n'est plus le
  choix recommandé pour un projet greenfield (Maintenance LTS, pas Active LTS).

## Conséquences

- Turbopack comme bundler par défaut (déjà stable en v16), builds de dev et de prod plus rapides.
- Aucun changement sur les principes directeurs du cahier des charges (Server Components pour le
  SEO, Client Components pour la recherche instantanée) : seule la version change, pas
  l'architecture.
- Fenêtre de support plus longue avant la prochaine migration majeure obligatoire (~2028).
