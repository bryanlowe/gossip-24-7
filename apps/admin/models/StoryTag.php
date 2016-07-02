<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class StoryTag extends ActiveRecord {
		const SCENARIO_STORY_TAG = 'story_tag';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_TAG] = ['story_tag_id', 'tag_name', 'description', 'source_link'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_tag';
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['tag_name', 'description'], 'required', 'on' => self::SCENARIO_STORY_TAG]
	        ];
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function saveStoryTag() {
	    	$db = Yii::$app->db;
	    	if($this->scenario == self::SCENARIO_STORY_TAG){
        		$tempValues = [];
        		foreach($this->attributes as $key => $value){
        			if($key != 'story_tag_id'){
        				$tempValues[$key] = $value;
        			}
        		} 
        		return $db->createCommand()->update('story_tag', $tempValues, 'story_tag_id = :p_id')->bindValues([':p_id' => $this->attributes['story_tag_id']])->execute();	    		
	    	}
	    }
	}
?>