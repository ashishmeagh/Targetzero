<?php
    if (DEBUG == 0){
        return [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:SERVER=targetzero-prod-sql.database.windows.net;DATABASE=TargetZero-Dev-SQL',
            'username' => 'TargetZeroAdmin',
            'password' => 'WhitingTargetZero@2021',
            'tablePrefix' => 'dbo',
        ];
    } else if(DEBUG == 1) {
        return array(
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:SERVER=targetzero-prod-sql.database.windows.net;DATABASE=TargetZero-Dev-SQL',
    'username' => 'TargetZeroAdmin',
    'password' => 'WhitingTargetZero@2021',
    'tablePrefix' => 'dbo',

    );

    }
    // return [
    //     'class' => 'yii\db\Connection',
    //     'dsn' => 'sqlsrv:SERVER=targetzero-stage-sqlserver.database.windows.net;DATABASE=TargetZero-Stage-DB',
    //     'username' => 'TargetZeroAdmin_stage',
    //     'password' => 'WhitingTargetZero@2022',
    //     'tablePrefix' => 'dbo',          
    // ];

