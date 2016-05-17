<?php
/**
 * @link https://github.com/AnatolyRugalev
 * @copyright Copyright (c) AnatolyRugalev
 * @license https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
 */

namespace understeam\scheduler;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class DbTask TODO: Write class description
 *
 * @property integer $id
 * @property string $key
 * @property string $expression
 * @property string $command
 * @property integer $createdAt
 * @property integer $updatedAt
 * @property integer $status
 *
 * @author Anatoly Rugalev
 * @link https://github.com/AnatolyRugalev
 */
class DbTask extends ActiveRecord implements TaskInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%scheduler_task}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ],
        ]);
    }

    /**
     * @param string $key
     * @param null $status
     * @return null|TaskInterface
     */
    public static function get($key, $status = null)
    {
        return static::find()
            ->andWhere(['key' => $key])
            ->andFilterWhere(['status' => self::STATUS_ACTIVE])
            ->one();
    }

    public function init()
    {
        $this->status = self::STATUS_ACTIVE;
        parent::init();
    }

    /**
     * @return \Iterator|DbTask[]
     */
    public static function getAll()
    {
        return static::find()->andWhere(['status' => self::STATUS_ACTIVE])->each();
    }

    /**
     * @return boolean
     */
    public function saveTask()
    {
        return $this->save(false);
    }

    public function deleteTask()
    {
        $this->status = self::STATUS_DELETED;
        $this->save(false);
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->unserialize($this->getAttribute('command'));
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->setAttribute('command', $this->serialize($command));
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->getAttribute('expression');
    }

    /**
     * @param string $expression
     */
    public function setExpression($expression)
    {
        $this->setAttribute('expression', $expression);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getAttribute('key');
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->setAttribute('key', $key);
    }

    protected function serialize($command)
    {
        return serialize($command);
    }

    protected function unserialize($string)
    {
        return unserialize($string);
    }
}
