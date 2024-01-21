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

namespace neoacevedo\auditing\behaviors;

use neoacevedo\auditing\models\Auditing;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Class AuditingBehavior
 *
 * @property \yii\db\ActiveRecord $owner
 */
class AuditBehavior extends Behavior
{

    /**
     * Array con atributos modificados del registro activo.
     * @var array
     */
    private $_oldAttributes = [];

    /**
     * Array with fields to ignore
     * @var array
     */
    public $ignored = ['created', 'updated', 'created_at', 'updated_at', 'createdAt', 'updatedAt', 'timestamp'];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * Guarda los atributos modificados después de encontrar el registro activo.
     */
    public function afterFind()
    {
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     * Guarda los atributos modificados después de insertar los datos del registro activo.
     */
    public function afterInsert()
    {
        $this->audit('CREATE');
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     * Guarda los atributos modificados después de actualizar los datos del registro activo.
     */
    public function afterUpdate()
    {
        $this->audit('UPDATE');
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     * Guarda los atributos modificados después de borrar los datos del registro activo.
     */
    public function afterDelete()
    {
        $this->audit('DELETE');
        $this->setOldAttributes([]);
    }

    /**
     * @return array
     */
    public function getOldAttributes()
    {
        return $this->_oldAttributes;
    }

    /**
     * @param $value
     */
    public function setOldAttributes($value)
    {
        $this->_oldAttributes = $value;
    }

    /**
     * Registra los cambios en la tabla `auditing`.
     * @param string $event Evento a registrar (INSERT|UPDATE|DELETE)
     * @throws \yii\db\Exception
     */
    protected function audit(string $event)
    {
        // If this is a delete then just write one row and get out of here
        if ($event === 'DELETE') {
            $controllerClass = new \ReflectionClass(Yii::$app->controller);
            $audit = new Auditing();
            $user_id = !Yii::$app->user->isGuest ? explode("-", Yii::$app->user->id)[1] : null;
            $username = !Yii::$app->user->isGuest ? Yii::$app->user->identity->username : 'guess';
            $audit->user_id = $user_id;
            $audit->description = 'User ' . $username . " deleted "
                . get_class($this->owner)
                . '[' . $this->getNormalizedPk() . '].';
            $audit->event = "DELETE";
            $audit->model = get_class($this->owner);
            $audit->attribute = "NULL";
            $audit->action = $controllerClass->getName() . '::' . Yii::$app->requestedAction->actionMethod . "()";
            $audit->ip = Yii::$app->request->remoteIP;
            // $audit->created_at = time();
            if (!$audit->save()) {
                foreach ($audit->errors as $key => $error) {
                    Yii::error($error[0], 'audit');
                }
            }
        } else {
            // Now lets actually write the attributes
            $this->auditAttributes($event);
        }
    }

    /**
     * Registra los eventos de inserción o actualización del registro activo.
     * @param string $event Evento a registrar (INSERT|UPDATE)
     * @throws \yii\db\Exception
     */
    protected function auditAttributes(string $event)
    {
        // Get the new and old attributes
        $newAttributes = $this->owner->getAttributes();
        $oldAttributes = $this->getOldAttributes();

        foreach ($newAttributes as $key => $value) {
            if (in_array($key, $this->ignored)) {
                continue;
            }
            if (!empty($oldAttributes)) {
                $old_value = (string) $oldAttributes[$key];
            } else {
                $old_value = 'NULL';
            }

            $controllerClass = new \ReflectionClass(Yii::$app->controller);

            if ($old_value !== $value) {
                $user_id = !Yii::$app->user->isGuest ? explode("-", Yii::$app->user->id)[1] : null;
                $username = !Yii::$app->user->isGuest ? Yii::$app->user->identity->username : 'guess';

                $audit = new Auditing();
                $audit->user_id = $user_id;
                $audit->description = 'User ' . $username . " " . strtolower($event) . " "
                    . get_class($this->owner)
                    . '[' . $this->getNormalizedPk() . '].';
                $audit->event = $event;
                $audit->model = get_class($this->owner);
                $audit->attribute = $key;
                $audit->old_value = $old_value;
                $audit->new_value = $value;
                $audit->action = $controllerClass->getName() . '::' . Yii::$app->requestedAction->actionMethod . "()";
                $audit->ip = Yii::$app->request->remoteIP;
                // $audit->created_at = time();
                if (!$audit->save()) {
                    foreach ($audit->errors as $key => $error) {
                        Yii::error($error[0], 'audit');
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    protected function getNormalizedPk()
    {
        $pk = $this->owner->getPrimaryKey();
        return is_array($pk) ? json_encode($pk) : $pk;
    }
}