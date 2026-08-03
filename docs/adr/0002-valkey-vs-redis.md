# ADR-0002 : Valkey remplace Redis pour le cache, le lock distribué et le rate limiting

**Statut :** Accepté
**Date :** 2026-08-03

## Contexte

Le cahier des charges (§3.2) prévoit Redis pour le cache applicatif, les verrous distribués
(Symfony Lock), le rate limiting (Symfony Rate Limiter) et les sessions.

Depuis la version 8.0, Redis est distribué sous licence AGPL (après un passage par la SSPL en
7.4). HireFlow étant un produit SaaS commercial, l'équipe a préféré éviter une dépendance
d'infrastructure sous licence copyleft forte, même si l'usage prévu (cache/lock internes, sans
redistribution du logiciel) ne pose probablement pas de risque réel.

Valkey est un fork de Redis maintenu par la Linux Foundation, sous licence BSD, compatible au
niveau protocole (RESP) avec Redis. Aucun changement côté client n'est nécessaire : l'extension
PHP `redis` (PECL) fonctionne à l'identique face à un serveur Valkey.

## Décision

On utilise **Valkey 8** (image `valkey/valkey:8-alpine`) partout où le cahier des charges prévoit
Redis. Le service Docker Compose s'appelle `valkey` (et non `redis`) — les DSN applicatifs futurs
utiliseront donc `redis://valkey:6379` (le préfixe de schéma reste `redis://` car c'est le nom du
protocole, pas du service).

## Alternatives considérées

- **Redis 8 (AGPL)** : rejeté par prudence légale pour un produit commercial, bien que l'usage
  interne prévu ne semble pas juridiquement problématique en pratique.

## Conséquences

- Aucun changement de code applicatif : le client PHP `redis` et les intégrations Symfony
  (Cache, Lock, Rate Limiter, Messenger transport Redis) sont inchangés.
- Attention au nommage du service dans toute config future (`.env`, `compose.yaml`) : `valkey`,
  pas `redis`.
- Écosystème d'outils un peu plus jeune que Redis historique, mais compatibilité protocole totale
  à ce jour.
