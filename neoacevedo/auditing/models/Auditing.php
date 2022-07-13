<?php

/*
 * Copyright (C) 2022 Néstor Acevedo <soporte at neoacevedo.co>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['event', 'model', 'attribute'], 'required'],
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
