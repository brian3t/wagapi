<?php

/* @var $this yii\web\View */

/* @var $searchModel app\models\BandSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Band';
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
$this->registerJsFile('/js/band/index.js',['position'=>yii\web\View::POS_END, 'depends' => [\app\assets\AppAsset::class]])
?>
<div class="band-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Band', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="search-form" style="display:none">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="yt_vid_popover" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Moderate Youtube video</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe class="embed-responsive-item" src="" id="ytlink_first" allowscriptaccess="always" frameborder="0" allow="autoplay,encrypted-media" width="560" height="315" allowfullscreen="">
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="vid_approve btn btn-sm btn-primary" data-is_approve="1" data-dismiss="modal">Approve</button>&nbsp;&nbsp;
                    <button type="button" class="vid_approve btn btn-sm btn-secondary" data-is_approve="0" data-dismiss="modal">Disapprove</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    $content = '<div class="embed-responsive">
</div>';

    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'expandOneOnly' => true
        ],
        ['attribute' => 'id', 'visible' => false],
        ['attribute' => 'name',
            'format' => 'raw',
            'contentOptions' => ['class' => 'name'],
            'value' => function ($model) {
                return "<a href=/band/view?id=" . $model->id . ">{$model->name}</a><br/><br/>"
                    .'<a href="https://google.com/search?newwindow=1&q=&quot;' . $model->name . '&quot;" target="_blank">Google search</a>';
            }],
        [
            'attribute' => 'user_id',
            'label' => 'User',
            'value' => function ($model) {
                if ($model->user) {
                    return $model->user->username;
                } else {
                    return NULL;
                }
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \yii\helpers\ArrayHelper::map(\app\models\User::find()->asArray()->all(), 'id', 'username'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'User', 'id' => 'grid--user_id']
        ],
        'logo:image',
        [
            'attribute' => 'ytlink_approved',
            'format' =>'raw',
            'value' => function($m){
                $approved = $m->ytlink_approved;
                if (! ($m->ytlink_first)) {return 'No Youtube Video';}
                $return = ($approved === 1 ? '<b>Approved</b>' : ($approved === 0 ? 'Disproved' : 'Not reviewed yet'));
                $return .= '<button type="button" class="btn btn-default btn-secondary" data-toggle="modal" data-vidid="'.$m->ytlink_first.'" data-target="#yt_vid_popover" 
                data-bandid="'.$m->id.'">Watch Youtube video</button>';
                return $return;
            }
        ],
        'type',
        'genre',
//        [
//            'attribute' => 'ytlink_first',
//            'format' => 'raw',
//            'value' => function ($m) {
//                /** @var $m \app\models\Band */
//                if (! ($m->ytlink_first)) return '';
//                if ($m->ytlink_first_tnail) return Html::a("<img src='" . $m->ytlink_first_tnail . "'/>", "https://www.youtube.com/watch?v=" . $m->ytlink_first, ['target' => '_blank']);
//                return Html::a($m->ytlink_first, "https://www.youtube.com/watch?v=" . $m->ytlink_first, ['target' => '_blank']);
//            }],
        /*[
            'class' => '\kartik\grid\BooleanColumn',
            'trueLabel' => 'Yes',
            'falseLabel' => 'No',
            'attribute' => 'ytlink_approved'
        ],*/
        'similar_to',
        'hometown_city',
        'website:url',
        'youtube:url',
        'instagram:url',
        'facebook',
        'created_at:datetime',
        'lno_score',
        'twitter',
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'filterModel' => $searchModel,
//        'floatHeader'=>true,
        'responsive' => true,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-band']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        'export' => false,
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]),
        ],
    ]); ?>

</div>
<?php
$this->registerJs('$("body").on("keyup.yiiGridView", "#w6 .filters input", function(e){
    let $e = $(this)
    if ($e.val().length > 1) {
        $("#w6").yiiGridView("applyFilter");
    }
})', \yii\web\View::POS_READY);
?>
