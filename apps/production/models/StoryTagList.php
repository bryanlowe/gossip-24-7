<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class StoryTagList extends ActiveRecord {
		const SCENARIO_STORY_TAG_LIST = 'story_tag_list';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_TAG_LIST] = ['story_tag_list_id', 'story_id', 'story_tag_id'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_tag_list';
	    }

	    /**
	     * Creates a relation between story and story image tables
	     */
	    public function getStory(){
	        return $this->hasOne(Story::className(), ['story_id' => 'story_id']);
	    }

	    /**
	     * Creates a relation between story and story tag tables
	     */
	    public function getStoryTag(){
	        return $this->hasOne(StoryTag::className(), ['story_tag_id' => 'story_tag_id']);
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['story_id', 'story_tag_id'], 'required', 'on' => self::SCENARIO_STORY_TAG_LIST],
	            [['story_id', 'story_tag_id'], 'integer', 'on' => self::SCENARIO_STORY_TAG_LIST]
	        ];
	    }
	}
?>