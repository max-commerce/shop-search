<?php

namespace maxcom\search\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

/**
 * View controller
 */
class AjaxController extends Controller
{

    public $category;
    public $product;

    /**
     * @return mixed
     */
    public function init()
    {
        $this->category = Yii::$app->category;
        $this->product  = Yii::$app->product;
    }

    /**
     * @return mixed
     */
    public function actionIndex($q)
    {

        if (empty($q)) {
            throw new yii\web\BadRequestHttpException('Bad Request.');
        }


        if ($this->category->hasAttribute('title')) {
            // maxcommerce v.1
            $titleAttribute = 'title';
        } elseif ($this->category->hasAttribute('name')) {
            // maxcommerce v.2
            $titleAttribute = 'name';
        } else {
            throw new ErrorException("Can't to allocate category title attribute");
        }

        $categories = $this->category->find()
                ->where(['like', $titleAttribute, $q])
                ->limit(10)
                ->all();

        $resultCategories = ArrayHelper::toArray($categories, [
          get_class($this->category) => [
            'value' => function($model){
                return ($model->parent ? $model->parent->title . ' / ' : '') . $model->title . ' »';
            },
            'url' => 'url',
          ]
        ]);

        if ($this->product->hasAttribute('title')) {
            // maxcommerce v.1
            $titleAttribute = 'title';
        } elseif ($this->product->hasAttribute('name')) {
            // maxcommerce v.2
            $titleAttribute = 'name';
        } else {
            throw new ErrorException("Can't to allocate product title attribute");
        }

        $products = $this->product->find()
                ->where(['like', $titleAttribute, $q])
                ->limit(20 - count($resultCategories))
                ->all();

        $resultProducts = ArrayHelper::toArray($products, [
          get_class($this->product) => [
            'value' => 'title',
            'url' => 'url',
          ]
        ]);

        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;        
        return ArrayHelper::merge(
            $resultCategories,
            $resultProducts
        );

    }
}
