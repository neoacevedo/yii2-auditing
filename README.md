Yii2 Auditing
=============
Registra cambios de sus modelos ActiveRecord de Yii2.

Este paquete permite mantener un historial de cambios de los modelos proveyendo información sobre posibles discrepancias o anomalías en la información que puedan indicar actividades sospechosas. La información recibida y almacenada se puede posteriormente desplegar de diversas maneras.

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
        [
                'class' => \neoacevedo\auditing\behaviors\AuditBehavior::class,
                'deleteOldData' => true, // Para borrar datos antiguos del registro de eventos
                'deleteNumRows' => 20, // Borra esta cantidad de registros
                'exclude' => ['foo', 'bar'] // No registra en el registro de eventos estos atributos del modelo
        ],
        ...
    ];
}
```

Desplegando la información
---

Puede desplegar la información como cualquier modelo que haya implementado dentro de su aplicación web.

Puede hacer uso de un controlador y una vista que use `GridView` para listar el historial. Por ejemplo, puede crear un controllador que se llame `AuditingController` y crear el método `actionIndex` como lo siguiente:

```php
    /**
     * Lists all Auditing models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuditingSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
```

Para visualizar los datos, crear el método `actionView`:

```php
    /**
     * Displays a single Auditing model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
```

Dentro de la vista `view` puede agregar el `GridView` para listar el histórico:

```php
...
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'user_id',
            'description',
            'event',
            'model',
            'attribute',
            'old_value',
            'new_value',
            'action',
            'ip',
            'created_at',
        ],
    ]); ?>
...
```
