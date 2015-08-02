<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class Story extends ActiveRecord {
		const SCENARIO_STORY = 'story';
		const SCENARIO_STORY_ID = 'story_id';
		const SCENARIO_STORY_VISIBILITY = 'visible';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY] = ['story_id', 'title', 'link', 'description', 'story_date', 'story_type', 'visible'];
	        $scenarios[self::SCENARIO_STORY_ID] = ['story_id'];
	        $scenarios[self::SCENARIO_STORY_VISIBILITY] = ['story_id', 'visible'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['story_id', 'title', 'link', 'description', 'story_type'], 'required', 'on' => self::SCENARIO_STORY],
	            [['story_id', 'visible'], 'required', 'on' => self::SCENARIO_STORY_VISIBILITY],
	            [['story_id'], 'required', 'on' => self::SCENARIO_STORY_ID]
	        ];
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function saveStory() {
	    	$db = Yii::$app->db;
	    	if($this->scenario == self::SCENARIO_STORY){
        		$tempValues = [];
        		foreach($this->attributes as $key => $value){
        			if($key != 'story_id'){
        				$tempValues[$key] = $value;
        			}
        		} 
        		if($this->attributes['story_id'] > 0){
        			return $db->createCommand()->update('story', $tempValues, 'story_id = :s_id')->bindValue(':s_id', $this->attributes['story_id'])->execute();
        		} else {
        			$returnVal = 0;
        			$returnVal += $db->createCommand()->insert('story', $tempValues)->execute();
        			if($returnVal){
        				$result = $db->createCommand('SELECT story_id FROM story ORDER BY story_id DESC')->queryOne();
        				if($result != false){
        					$returnVal += $db->createCommand()->insert('story_priority', ['priority' => 0, 'story_id' => $result['story_id']])->execute();
        				}
        			}
        			return $returnVal;
        		}	    		
	    	} else if($this->scenario == self::SCENARIO_STORY_VISIBILITY){
        		$tempValues = [];
        		foreach($this->attributes as $key => $value){
        			if($key != 'story_id' && $value != ""){
        				$tempValues[$key] = $value;
        			}
        		} 
        		return $db->createCommand()->update('story', $tempValues, 'story_id = :s_id')->bindValue(':s_id', $this->attributes['story_id'])->execute();   		
	    	} 
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function deleteStory() {
	    	$returnVal = 0;
	    	$db = Yii::$app->db;
	    	if($this->scenario == self::SCENARIO_STORY_ID){
	    		$returnVal += $db->createCommand('DELETE FROM story WHERE story_id = :id')->bindValue(':id', $this->attributes['story_id'])->execute();    		
        		$returnVal += $db->createCommand('DELETE FROM story_priority WHERE story_id = :id')->bindValue(':id', $this->attributes['story_id'])->execute();    		
	    	}
	    	return $returnVal;
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function loadStories() {
	    	$db = Yii::$app->db;
	    	return $db->createCommand('SELECT * FROM story INNER JOIN story_priority ON story.story_id = story_priority.story_id ORDER BY story.story_id DESC')->queryAll();
	    }
	}
?>