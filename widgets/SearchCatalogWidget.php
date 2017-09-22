<?php

namespace maxcom\search\widgets;

use Yii;
use yii\helpers\Url;

class SearchCatalogWidget extends \kartik\typeahead\Typeahead
{
  public function init(){

      $this->name = $this->name ? $this->name : 'q';
      $this->pluginOptions = $this->pluginOptions ? $this->pluginOptions : [
          'highlight' => true
      ];
      $this->options = $this->options ? $this->options : [
          'placeholder' => 'Поиск по каталогу..',
      ];
      $this->value = $this->value ? $this->value : Yii::$app->request->get("q");
      $this->dataset = $this->dataset ? $this->dataset : [
          [
              'limit' => 20,
              'display' => 'value',
              'remote' => [
                  'url' => Url::to(['/search/ajax']) . '?q=%QUERY',
                  'wildcard' => '%QUERY'
              ],
              'templates' => [
                  'notFound' => '<div class="search-widget-empty-results">Нет результатов</div>',
                  'suggestion' => '',
              ]
          ]
      ];
      $this->pluginEvents = $this->pluginEvents ? $this->pluginEvents : [
          "typeahead:select" => "function(obj, selected, name) { window.location = selected.url }",
      ];

      parent::init();
  }
}