<?php

use yii\db\Migration;

class m220401_160544_common_config_value extends Migration
{
    private string $tableName = '{{%common_config_value}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'merchant_id' => $this->integer()->null()->defaultValue(0)->comment('Merchant'),
            'app_id' => $this->string(20)->notNull()->defaultValue('')->comment('App ID'),
            'config_id' => $this->integer()->notNull()->defaultValue(0)->comment('Config ID'),
            'data' => $this->text()->null()->comment('Config content'),
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Common Configuration value table');
        
        $this->createIndex('Idx_ConfigId',$this->tableName,'config_id',0);

        $this->insert($this->tableName, ['id'=>'1','app_id'=>'backend','config_id'=>'6','merchant_id'=>'0','data'=>'']);
        $this->insert($this->tableName, ['id'=>'2','app_id'=>'backend','config_id'=>'1','merchant_id'=>'0','data'=>'© 2016 - 2020 Yii2-AIO All Rights Reserved.']);
        $this->insert($this->tableName, ['id'=>'3','app_id'=>'backend','config_id'=>'60','merchant_id'=>'0','data'=>'']);
        $this->insert($this->tableName, ['id'=>'4','app_id'=>'backend','config_id'=>'59','merchant_id'=>'0','data'=>'']);
        $this->insert($this->tableName, ['id'=>'5','app_id'=>'backend','config_id'=>'4','merchant_id'=>'0','data'=>'浙ICP备17025911号-1']);
        $this->insert($this->tableName, ['id'=>'6','app_id'=>'backend','config_id'=>'2','merchant_id'=>'0','data'=>'Yii2-AIO']);
        $this->insert($this->tableName, ['id'=>'7','app_id'=>'backend','config_id'=>'5','merchant_id'=>'0','data'=>'']);
        $this->insert($this->tableName, ['id'=>'8','app_id'=>'backend','config_id'=>'7','merchant_id'=>'0','data'=>'']);
        $this->insert($this->tableName, ['id'=>'9','app_id'=>'backend','config_id'=>'52','merchant_id'=>'0','data'=>'1']);
        $this->insert($this->tableName, ['id'=>'10','app_id'=>'backend','config_id'=>'55','merchant_id'=>'0','data'=>'1']);
        $this->insert($this->tableName, ['id'=>'11','app_id'=>'backend','config_id'=>'53','merchant_id'=>'0','data'=>'0']);
        $this->insert($this->tableName, ['id'=>'12','app_id'=>'backend','config_id'=>'90','merchant_id'=>'0','data'=>'0']);
        $this->insert($this->tableName, ['id'=>'13','app_id'=>'backend','config_id'=>'64','merchant_id'=>'0','data'=>'1']);
        $this->insert($this->tableName, ['id'=>'14','app_id'=>'backend','config_id'=>'61','merchant_id'=>'0','data'=>'1']);
        
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('Idx_ConfigId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1;');
    }
}

