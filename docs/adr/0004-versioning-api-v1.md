# ADR-0004 : Versioning de l'API via préfixe /api/v1

**Statut :** Accepté
**Date :** 2026-08-05

## Contexte

Le cahier des charges introduit, sans le mentionner explicitement comme convention globale,
un préfixe `/api/v1` dans l'exemple d'authentification (§3.3, séquence de login :
`POST /api/v1/auth/login`). Les premières ressources API Platform du module Identity & Access
(`/api/users`, `/api/users/{id}/verify_email`) avaient été mises en place sans ce préfixe,
créant une incohérence entre les routes CRUD (API Platform) et les routes d'action
(Controllers Symfony classiques comme le login).

Deux mécanismes de préfixe existent en parallèle dans une installation API Platform/Symfony :

- `config/packages/api_platform.yaml` (`api_platform.defaults.route_prefix`)
- `config/routes/api_platform.yaml` (route d'import avec sa propre clé `prefix`)

Le second l'emporte en pratique sur le premier lorsque les deux sont définis — ce qui a causé
une confusion initiale (le changement dans `packages/api_platform.yaml` semblait ignoré).

## Décision

Toutes les routes de l'API, qu'elles soient générées par API Platform ou écrites en Controller
Symfony classique, sont préfixées par **`/api/v1`**. Le préfixe est déclaré à un seul endroit :
`config/routes/api_platform.yaml` (clé `prefix`). Aucune configuration de préfixe n'est laissée
dans `config/packages/api_platform.yaml`, pour éviter d'avoir deux sources de vérité dont une
silencieusement ignorée.

Les routes d'action non-CRUD (login, refresh, futures actions similaires) sont déclarées avec
leur chemin complet en dur dans l'attribut `#[Route('/api/v1/...')]`, en cohérence manuelle avec
ce même préfixe.

## Alternatives considérées

- **Pas de versioning pour l'instant** : rejeté — le cahier des charges donne explicitement `/v1`
  dans son exemple de référence pour l'authentification ; s'en écarter aurait introduit une
  incohérence avec le document source dès le premier sprint fonctionnel.
- **Versioning uniquement sur les routes d'action, pas sur les ressources API Platform** :
  rejeté — aurait laissé une incohérence structurelle entre deux familles de routes de la même
  API, source de confusion pour les consommateurs de l'API (frontend, back-office, intégrations
  futures).

## Conséquences

- Toute nouvelle ressource API Platform ou tout nouveau Controller d'action doit respecter le
  préfixe `/api/v1` dès sa création.
- Une montée de version future (`/api/v2`) nécessitera une stratégie de coexistence
  (versions parallèles, dépréciation progressive) — non traitée par cet ADR, à documenter
  séparément le moment venu.
- Le double mécanisme de préfixe (`packages/` vs `routes/`) reste une source de confusion
  potentielle pour l'équipe ; ce point est documenté ici pour éviter de reproduire l'erreur.
