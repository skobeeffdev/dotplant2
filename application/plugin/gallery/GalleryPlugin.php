<?php

namespace app\plugin\gallery;

use app\modules\plugin\components\AbstractPlugin;
use app\modules\plugin\PluginModule;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\db\Schema;
use yii\helpers\Json;

class GalleryPlugin extends AbstractPlugin implements BootstrapInterface
{
    public $defaultRoute = 'main';
    static protected $pluginId = 'gallery';
    static protected $configureRoute = '/plugin/gallery/configure/index';
    static protected $routes = [
        'gal/index' => 'plugin/gallery/galleries/index',
    ];
    static protected $widgetsMap = [
        'gallery' => 'Gallery'
    ];

    private $config = [];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $this->config = $this->getConfig();
    }

    public function init()
    {
        parent::init();
    }

    protected function getConfig()
    {
        $config = parent::getConfig();
        return empty($config) ? [] : Json::decode($config);
    }


    public function installPlugin(PluginModule $module)
    {
        $config = [
            'core.directory' => '@webroot/theme/resources/gallery/',
            'core.path' => '/theme/resources/gallery/',
        ];
        parent::updateConfig(Json::encode($config));

        $db = \Yii::$app->db;
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $db->createCommand()->createTable('{{%plugin_gallery_galleries}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'path' => Schema::TYPE_STRING . ' NOT NULL',
                'description' => Schema::TYPE_STRING,
                'options' => Schema::TYPE_TEXT,
                'sort_order' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            ],
            $tableOptions
        )->execute();
        $db->createCommand()->createTable('{{%plugin_gallery_images}}',
            [
                'id' => Schema::TYPE_PK,
                'gallery_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
                'file_name' => Schema::TYPE_STRING . ' NOT NULL',
                'file_path' => Schema::TYPE_STRING . ' NOT NULL',
                'description' => Schema::TYPE_STRING,
                'sort_order' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            ],
            $tableOptions
        )->execute();
        $db->createCommand()->createTable('{{%plugin_gallery_thumbs}}',
            [
                'id' => Schema::TYPE_PK,
                'image_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'thumb_type' => Schema::TYPE_STRING . ' NOT NULL',
                'file_name' => Schema::TYPE_STRING . ' NOT NULL',
                'file_path' => Schema::TYPE_STRING . ' NOT NULL',
            ],
            $tableOptions
        )->execute();
        $db->createCommand()->createTable('{{%plugin_gallery_images_objects}}',
            [
                'id' => Schema::TYPE_PK,
                'object_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'object_model_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'image_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'sort_order' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            ],
            $tableOptions
        )->execute();
    }

    public function uninstallPlugin(PluginModule $module)
    {
        $db = \Yii::$app->db;
        foreach (['{{%plugin_gallery_galleries}}', '{{%plugin_gallery_images}}', '{{%plugin_gallery_thumbs}}', '{{%plugin_gallery_images_objects}}',] as $table) {
            $db->createCommand()->dropTable($table)->execute();
        }
    }

    public function reinstallPlugin(PluginModule $module)
    {
    }

}
?>