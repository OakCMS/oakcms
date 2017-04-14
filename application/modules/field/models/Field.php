<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\field\models;

use Yii;
use yii\helpers\ArrayHelper;

class Field extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%field}}';
    }

    public function rules()
    {
        return [
            [['name', 'slug', 'relation_model'], 'required'],
            [['category_id'], 'integer'],
            [['name', 'type', 'description', 'relation_model'], 'string'],
            ['slug', 'unique'],
            ['model_category_id', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('field', 'Name'),
            'slug' => Yii::t('field', 'Slug'),
            'description' => Yii::t('field', 'Description'),
            'options' => Yii::t('field', 'Options'),
            'type' => Yii::t('field', 'Type'),
            'category_id' => Yii::t('field', 'Category ID'),
            'relation_model' => Yii::t('field', 'Relation model'),
        ];
    }

    public function getVariants()
    {
        return $this->hasMany(FieldVariant::className(), ['field_id' => 'id']);
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = Field::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(is_array($this->model_category_id)) {
                $this->model_category_id = json_encode($this->model_category_id);
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterFind()
    {
        $this->model_category_id = json_decode($this->model_category_id);

        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function getCategory()
    {
		return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}

    public function beforeDelete()
    {
        foreach ($this->hasMany(FieldValue::className(), ['field_id' => 'id'])->all() as $frv) {
            $frv->delete();
        }

        foreach ($this->hasMany(FieldVariant::className(), ['field_id' => 'id'])->all() as $fv) {
            $fv->delete();
        }

		return true;
    }
}
