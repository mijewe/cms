<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\db;

use craft\helpers\Db;
use craft\helpers\StringHelper;

/**
 * Active Record base class.
 *
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string $uid UUID
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @since 3.4.0
     */
    public function __set($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $value = $this->_prepareValue($name, $value);
        }
        parent::__set($name, $value);
    }

    /**
     * @inheritdoc
     * @since 3.4.0
     */
    public function setAttribute($name, $value)
    {
        $value = $this->_prepareValue($name, $value);
        parent::setAttribute($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->prepareForDb();
        return parent::beforeSave($insert);
    }

    /**
     * Sets the `dateCreated`, `dateUpdated`, and `uid` attributes on the record.
     *
     * @since 3.1.0
     */
    protected function prepareForDb()
    {
        $now = Db::prepareDateForDb(new \DateTime());

        if ($this->getIsNewRecord()) {
            if ($this->hasAttribute('dateCreated') && !isset($this->dateCreated)) {
                $this->dateCreated = $now;
            }

            if ($this->hasAttribute('dateUpdated') && !isset($this->dateUpdated)) {
                $this->dateUpdated = $now;
            }

            if ($this->hasAttribute('uid') && !isset($this->uid)) {
                $this->uid = StringHelper::UUID();
            }
        } else if (
            !empty($this->getDirtyAttributes()) &&
            $this->hasAttribute('dateUpdated')
        ) {
            if (!$this->isAttributeChanged('dateUpdated')) {
                $this->dateUpdated = $now;
            } else {
                $this->markAttributeDirty('dateUpdated');
            }
        }
    }

    /**
     * Prepares a value to be saved to the database.
     *
     * @param string $name The attribute name
     * @param mixed $value The attribute value
     * @return mixed The prepared value
     * @since 3.4.0
     */
    private function _prepareValue(string $name, $value)
    {
        $value = Db::prepareValueForDb($value);

        $columns = static::getTableSchema()->columns;
        if (isset($columns[$name])) {
            $value = $columns[$name]->phpTypecast($value);
        }

        return $value;
    }
}
