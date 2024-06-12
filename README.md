# Ecommerce

Un projet ecommerce avec Symfony 7. On démarre ça et on verra jusqu'où on va...

## Installation

Vous avez besoin d'installer :

- Docker
- PHP 8.2

Vous devez lancer les containers Docker :

```bash
docker compose up -d
```

## Testing

On utilise une base de données SQLite pour les tests.

```bash
php bin/console --env=test d:d:c
php bin/console --env=test d:s:c
```
