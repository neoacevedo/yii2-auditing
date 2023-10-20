Yii2 Auditing
=============
Registra cambios de sus modelos ActiveRecord de Yii2

Instalación
------------

La forma preferida de instalar esta extensión es a través de [composer](http://getcomposer.org/download/).

Luego ejecute

```
php composer.phar require --prefer-dist neoacevedo/yii2-auditing "*"
```

o agregue

```
"neoacevedo/yii2-auditing": "*"
```

 a la sección require de su archivo `composer.json`.


Uso
-----

Una vez que la extensión está instalada, en el archivo de configuración de la consola de su aplicación, agregue en la zona `migrationPath`

```php
...
'@vendor/neoacevedo/yii2-auditing/neoacevedo/auditing/migrations',
...
```


luego, agregue en el código de su modelo dentro del método `behaviors`:

```php
public function behaviors()
{
    return [
        \neoacevedo\auditing\behaviors\AuditBehavior::class,
        ...
    ];
}
```
