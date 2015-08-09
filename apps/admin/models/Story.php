<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class Story extends ActiveRecord {
		const SCENARIO_STORY = 'story';
		const SCENARIO_STORY_VISIBILITY = 'visible';

		/**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story';
	    }

	    /**
	     * Creates a relation between story and story priority tables
	     */
	    public function getStoryPriority(){
	        return $this->hasOne(StoryPriority::className(), ['story_id' => 'story_id']);
	    }

	    /**
	     * Creates a relation between story and story image tables
	     */
	    public function getStoryImage(){
	        return $this->hasMany(StoryImage::className(), ['story_id' => 'story_id']);
	    }

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY] = ['story_id', 'title', 'link', 'description', 'story_date', 'story_type', 'visible'];
	        $scenarios[self::SCENARIO_STORY_VISIBILITY] = ['story_id', 'visible'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['title', 'link', 'description', 'story_type'], 'required', 'on' => self::SCENARIO_STORY],
	            [['visible'], 'required', 'on' => self::SCENARIO_STORY_VISIBILITY],
	            [['visible'], 'integer', 'on' => self::SCENARIO_STORY_VISIBILITY]
	        ];
	    }
	}
?>