Yii2 Auditing
=============
Record changes to your ActiveRecord models of Yii2. 

This package allows you to maintain a history of model changes by providing information on possible discrepancies or anomalies in the information that may indicate suspicious activity. Information received and stored can then be deployed in various ways. 

Instalation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist neoacevedo/yii2-auditing "*"
```

or add

```
"neoacevedo/yii2-auditing": "*"
```

to the required section of your `composer.json`.


Usage
-----

Once installed this extension, in your application config file, add into the `migrationPath` zone

```php
...
'@vendor/neoacevedo/yii2-auditing/neoacevedo/auditing/migrations',
...
```

Then, add in your model code within `behaviors` method:

```php
public function behaviors()
{
    return [
        [
            'class' => \neoacevedo\auditing\behaviors\AuditBehavior::class,
            'deleteOldData' => true, // To delete old data from the events log
            'deleteNumRows' => 20, // It deletes this number of records
            'ignored' => ['foo', 'bar'], // Do not stores in the events log these model attributes
        ],
        ...
    ];
}
```

Deploying information
---

You can deploy the information as any model you have implemented within your web application. 

You can use a driver and a view that uses .GridView to list the history. For example, you can create a controller called "AuditingController" and create the "actionIndex" method as follows: 

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

To view the data, create the `actionView`` method:

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

Inside the view `view` you can add the `GridView`` to list the historic:

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
