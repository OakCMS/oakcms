<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%form_builder_field}}".
 *
 * @property integer $id
 * @property integer $form_id
 * @property integer $priority
 * @property string $type
 * @property string $label
 * @property string $slug
 * @property string $options
 * @property string $roles
 * @property string $data
 */
class FormBuilderField extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder_field}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_id', 'type', 'label', 'slug'], 'required'],
            [['form_id', 'priority'], 'integer'],
            [['options', 'roles', 'data'], 'string'],
            [['type', 'label', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => Yii::t('form_builder', 'ID'),
            'form_id'  => Yii::t('form_builder', 'Form ID'),
            'priority' => Yii::t('form_builder', 'Priority'),
            'type'     => Yii::t('form_builder', 'Type'),
            'label'    => Yii::t('form_builder', 'Label'),
            'slug'     => Yii::t('form_builder', 'Slug'),
            'options'  => Yii::t('form_builder', 'Options'),
            'roles'    => Yii::t('form_builder', 'Roles'),
            'data'     => Yii::t('form_builder', 'Data'),
        ];
    }

    public static function getJson($form_id) {
        $fields = self::find()->select(['data'])->where(['form_id' => $form_id])->asArray()->all();
        $return = [];
        foreach ($fields as $field) {
            $return[] = ArrayHelper::getValue($field, 'data', []);
        }
        return json_encode($return);
    }
}
