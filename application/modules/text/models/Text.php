<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\text\models;

use app\components\ActiveRecord;
use app\helpers\StringHelper;
use app\modules\admin\components\behaviors\SettingModel;
use dosamigos\translateable\TranslateableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Class Text
 * @package app\modules\text\models
 *
 * @property $id
 * @property $published_at
 * @property $created_at
 * @property $updated_at
 * @property $status
 * @property $enable_php_code
 * @property $php_code
 * @property $order
 * @property $settings
 *
 * @mixin SettingModel Settings Model
 */
class Text extends ActiveRecord
{
    const CACHE_KEY = 'oakcms_text';

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    public $output = '';

    public static function tableName()
    {
        return '{{%texts}}';
    }

    public static function getWereToPlace()
    {
        return [
            '0'  => Yii::t('text', 'On all pages'),
            '-'  => Yii::t('text', 'Not on the same page'),
            '1'  => Yii::t('text', 'On these pages only'),
            '-1' => Yii::t('text', 'On all pages, except for the above'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class'         => SettingModel::className(),
                'settingsField' => 'settings',
                'module'        => false,
            ],
            'trans'    => [
                'class'                 => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'title', 'subtitle', 'text', 'settings',
                ],
            ],
            'sortable' => [
                'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
                'query' => self::find(),
            ],
        ];
    }

    public function getTranslations()
    {
        return $this->hasMany(TextsLang::className(), ['texts_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['id', 'order', 'status', 'enable_php_code'], 'number', 'integerOnly' => true],
            [['title'], 'required'],
            [['title', 'subtitle', 'layout', 'links', 'text', 'php_code'], 'string'],
            ['text', 'trim'],
            [['slug', 'where_to_place'], 'string', 'max' => 150],

            ['published_at', 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            ['published_at', 'default', 'value' => time()],

            ['slug', 'default', 'value' => ''],
            [['settings'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text'            => Yii::t('text', 'Content'),
            'slug'            => Yii::t('text', 'Position'),
            'enable_php_code' => Yii::t('text', 'Enable php code'),
            'php_code'        => Yii::t('text', 'php code'),
            'title'           => Yii::t('text', 'Title'),
            'subtitle'        => Yii::t('text', 'Subtitle'),
            'layout'          => Yii::t('text', 'Layout'),
            'links'           => Yii::t('text', 'Links'),
            'where_to_place'  => Yii::t('text', 'Where To Place'),
            'published_at'    => Yii::t('admin', 'Published'),
            'status'          => Yii::t('admin', 'Status'),
        ];
    }

    public function beforeValidate()
    {
        if (is_array($this->links)) {
            $this->links = implode(",", $this->links);
        }

        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->links = explode(",", $this->links);
        parent::afterFind();
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        foreach ($this->getTranslations()->all() as $translation) {
            $translation->delete();
        }
    }

    /**
     * Batch copy items to a new category or current.
     *
     * @param   array $ids An array of row IDs.
     *
     * @return boolean.
     */
    public static function batchCopy($ids)
    {

        while (!empty($ids)) {
            $id = array_shift($ids);

            $model = self::findOne($id);
            $translations = $model->translations;
            $model->detachBehavior('tree');
            $model->id = null;
            $model->isNewRecord = true;
            if ($model->save()) {
                foreach ($translations as $translation) {
                    /** @var $translation TextsLang */
                    $translation->texts_id      = $model->id;
                    $translation->title         = StringHelper::increment($translation->title);
                    $translation->id            = null;
                    $translation->isNewRecord   = true;
                    $translation->save();
                }
            }
        }

        return true;
    }
}
