<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	class Story extends ActiveRecord {
		const SCENARIO_STORY = 'story';
	    const SCENARIO_IMAGES = 'story_image';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY] = ['story_id', 'title', 'link', 'description', 'story_date', 'story_type', 'visible'];
	        $scenarios[self::SCENARIO_IMAGES] = ['story_image_id', 'story_id', 'image_name', 'order'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['story_id', 'title', 'link', 'description', 'story_type'], 'required', 'on' => self::SCENARIO_STORY],
	            [['story_id', 'image_name'], 'required', 'on' => self::SCENARIO_IMAGES]
	        ];
	    }

	    /**
	     * @return boolean success or failure
	     */
	    public function saveStory() {
	    	$db = Yii::$app->db;
	    	if($this->scenario == self::SCENARIO_STORY){
        		$tempValues = [];
        		$storyID = $this->attributes['story_id'];
        		foreach($this->attributes as $key => $value){
        			if($key != 'story_id'){
        				$tempValues[$key] = $value;
        			}
        		} 
        		if($storyID > 0){
        			return $db->createCommand()->update('story', $tempValues, 'story_id = '.$storyID)->execute();
        		} else {
        			return $db->createCommand()->insert('story', $tempValues)->execute();
        		}	    		
	    	}
	    }
	}
?>