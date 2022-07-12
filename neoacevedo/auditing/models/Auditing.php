<?php

namespace neoacevedo\auditing\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auditing".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $description
 * @property string $event INSERT|UPDATE|DELETE
 * @property string $model
 * @property string $attribute
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string|null $action namespace\TheController::actionTheAction()
 * @property string|null $ip
 * @property string $created_at
 */
class Auditing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditing';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'updatedAtAttribute' => false
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['event', 'model', 'attribute'], 'required'],
            [['created_at'], 'safe'],
            [['description', 'model', 'attribute', 'old_value', 'new_value', 'action'], 'string', 'max' => 255],
            [['event', 'ip'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'description' => 'Description',
            'event' => 'Event (INSERT|UPDATE|DELETE)',
            'model' => 'Model',
            'attribute' => 'Attribute',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'action' => 'Controller Action (namespace\\TheController::actionTheAction())',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }
}
