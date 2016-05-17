<?php

$config = [
    'id' => 'app-console',
    'basePath' => \Yii::getAlias('@tests'),
    'runtimePath' => \Yii::getAlias('@tests/_output'),
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::className(),
            'migrationPath' => dirname(\Yii::getAlias('@tests')) . '/src/migrations',
        ]
    ],
    'components' => [
        'scheduler' => [
            'class' => \understeam\scheduler\Scheduler::className(),
            'executor' => [
                'class' => \understeam\scheduler\CommandBusExecutor::className(),
                'commandBus' => 'commandBus',
            ],
        ],
        'db' => [
            'class' => \yii\db\Connection::className(),
            'dsn' => 'sqlite:' . \Yii::getAlias('@tests/_output/scheduler.db'),
        ],
        'commandBus' => \trntv\bus\CommandBus::className(),
    ],
];

new yii\console\Application($config);
