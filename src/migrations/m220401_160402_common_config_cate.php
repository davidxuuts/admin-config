<?php

use yii\db\Migration;

class m220401_160402_common_config_cate extends Migration
{
    private string $tableName = '{{%common_config_cate}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'title' => $this->string(50)->notNull()->defaultValue('')->comment('Title'),
            'pid' => $this->integer()->null()->defaultValue(0)->comment('Parent ID'),
            'app_id' => $this->string(20)->notNull()->defaultValue('')->comment('App ID'),
            'level' => $this->tinyInteger(3)->defaultValue(1)->comment('Level'),
            'sort' => $this->integer()->defaultValue(0)->comment('Display order'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Common config category table');
        
        $this->createIndex('Idx_AppId', $this->tableName, 'app_id');

        $this->insert($this->tableName, ['id'=>'1','title'=>'网站配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'0', 'status'=>'1']);
        $this->insert($this->tableName, ['id'=>'2','title'=>'系统配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'3','title'=>'微信配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'4','title'=>'支付配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'3','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'5','title'=>'第三方登录','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'6','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'6','title'=>'邮件配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'7','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'7','title'=>'云存储','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'5','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'8','title'=>'支付宝','pid'=>'4','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'9','title'=>'微信','pid'=>'4','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'10','title'=>'银联','pid'=>'4','app_id'=>'backend','level'=>'2','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'11','title'=>'QQ登录','pid'=>'5','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'12','title'=>'微博登录','pid'=>'5','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'13','title'=>'微信登录','pid'=>'5','app_id'=>'backend','level'=>'2','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'14','title'=>'GitHub登录','pid'=>'5','app_id'=>'backend','level'=>'2','sort'=>'3','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'15','title'=>'七牛云','pid'=>'7','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'16','title'=>'邮件','pid'=>'6','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'17','title'=>'网站基础','pid'=>'1','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'18','title'=>'系统基础','pid'=>'2','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'19','title'=>'公众号','pid'=>'3','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'20','title'=>'阿里云OSS','pid'=>'7','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'21','title'=>'小程序','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'15','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'22','title'=>'基础配置','pid'=>'21','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'23','title'=>'图片处理','pid'=>'2','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'24','title'=>'App推送','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'13','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'25','title'=>'极光推送','pid'=>'24','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'26','title'=>'分享配置','pid'=>'3','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'27','title'=>'短信配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'8','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'28','title'=>'阿里云','pid'=>'27','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'29','title'=>'地图','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'12','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'30','title'=>'百度地图','pid'=>'29','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'31','title'=>'腾讯地图','pid'=>'29','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'32','title'=>'高德地图','pid'=>'29','app_id'=>'backend','level'=>'2','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'33','title'=>'腾讯COS','pid'=>'7','app_id'=>'backend','level'=>'2','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'34','title'=>'OAuth2','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'14','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'35','title'=>'授权配置','pid'=>'34','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'36','title'=>'系统配置','pid'=>'0','app_id'=>'merchant','level'=>'1','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'37','title'=>'系统基础','pid'=>'36','app_id'=>'merchant','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'38','title'=>'微信配置','pid'=>'0','app_id'=>'merchant','level'=>'1','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'39','title'=>'公众号','pid'=>'38','app_id'=>'merchant','level'=>'2','sort'=>'0', 'status'=>'1']);
        $this->insert($this->tableName, ['id'=>'40','title'=>'小程序','pid'=>'0','app_id'=>'merchant','level'=>'1','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'41','title'=>'基础配置','pid'=>'46','app_id'=>'merchant','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'42','title'=>'基础配置','pid'=>'40','app_id'=>'merchant','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'43','title'=>'物流追踪','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'10','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'44','title'=>'快递鸟','pid'=>'43','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'45','title'=>'快递100','pid'=>'43','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'46','title'=>'阿里云','pid'=>'43','app_id'=>'backend','level'=>'2','sort'=>'2','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'47','title'=>'聚合','pid'=>'43','app_id'=>'backend','level'=>'2','sort'=>'3','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'48','title'=>'商户配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'4','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'49','title'=>'注册相关','pid'=>'48','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'50','title'=>'会员配置','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'9','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'51','title'=>'基础配置','pid'=>'50','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'52','title'=>'个推推送','pid'=>'24','app_id'=>'backend','level'=>'2','sort'=>'1','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'53','title'=>'小票打印','pid'=>'0','app_id'=>'backend','level'=>'1','sort'=>'11','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'54','title'=>'易联云','pid'=>'53','app_id'=>'backend','level'=>'2','sort'=>'0','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'55','title'=>'Stripe','pid'=>'4','app_id'=>'backend','level'=>'2','sort'=>'3','status'=>'1']);
        $this->insert($this->tableName, ['id'=>'56','title'=>'AlphaPay','pid'=>'4','app_id'=>'backend','level'=>'2','sort'=>'4','status'=>'1']);

        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('Idx_AppId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1;');
    }
}
