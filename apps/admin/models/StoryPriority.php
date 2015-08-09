<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class StoryPriority extends ActiveRecord {
		const SCENARIO_STORY_PRIORITY = 'story_priority';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_PRIORITY] = ['story_priority_id', 'story_id', 'priority'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_priority';
	    }

	    /**
	     * Creates a relation between story and story priority tables
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
	            [['story_priority_id', 'story_id', 'priority'], 'required', 'on' => self::SCENARIO_STORY_PRIORITY],
	            [['story_priority_id', 'story_id', 'priority'], 'integer', 'on' => self::SCENARIO_STORY_PRIORITY]
	        ];
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function saveStory() {
	    	$db = Yii::$app->db;
	    	if($this->scenario == self::SCENARIO_STORY_PRIORITY){
        		$tempValues = [];
        		foreach($this->attributes as $key => $value){
        			if($key != 'story_id' && $key != 'story_priority_id' && $value != ""){
        				$tempValues[$key] = $value;
        			}
        		} 
        		return $db->createCommand()->update('story_priority', $tempValues, 'story_priority_id = :p_id AND story_id = :s_id')->bindValues([':p_id' => $this->attributes['story_priority_id'], ':s_id' => $this->attributes['story_id']])->execute();	    		
	    	}
	    }
	}
?>