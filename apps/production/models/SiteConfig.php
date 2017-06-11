<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class SiteConfig extends ActiveRecord {
		const SCENARIO_SITE_CONFIG = 'site_config';
		const SCENARIO_SITE_CONFIG_MAINTENANCE = 'maintenance';
		const PRODUCTION_SITE = 3;
		const STAGING_SITE = 2;

		/**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'site_config';
	    }

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_SITE_CONFIG] = ['site_config_id', 'site_mode', 'maintenance_mode'];
	        $scenarios[self::SCENARIO_SITE_CONFIG_MAINTENANCE] = ['site_config_id', 'maintenance_mode'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['maintenance_mode'], 'required', 'on' => self::SCENARIO_SITE_CONFIG_MAINTENANCE]
	        ];
	    }
	}
?>