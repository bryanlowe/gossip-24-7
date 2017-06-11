<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class StoryVideo extends ActiveRecord {
		const SCENARIO_STORY_VIDEO = 'story_video';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_VIDEO] = ['story_video_id', 'story_id', 'video_title', 'video_html'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_video';
	    }

	    /**
	     * Creates a relation between story and story video tables
	     */
	    public function getStory(){
	        return $this->hasOne(Story::className(), ['story_id' => 'story_id']);
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['story_id', 'video_title', 'video_html'], 'required', 'on' => self::SCENARIO_STORY_VIDEO]
	        ];
	    }
	}
?>