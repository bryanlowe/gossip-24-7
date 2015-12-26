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
	        $scenarios[self::SCENARIO_STORY] = ['story_id', 'title', 'link', 'description', 'story_date', 'story_size', 'show_desc', 'visible'];
	        $scenarios[self::SCENARIO_STORY_VISIBILITY] = ['story_id', 'visible', 'story_date'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['title', 'link', 'description', 'show_desc', 'story_size'], 'required', 'on' => self::SCENARIO_STORY],
	            [['visible'], 'required', 'on' => self::SCENARIO_STORY_VISIBILITY],
	            [['visible'], 'integer', 'on' => self::SCENARIO_STORY_VISIBILITY]
	        ];
	    }
	}

	class StoryTest extends \PHPUnit_Framework_TestCase
	{
	    protected function setUp()
	    {
	        parent::setUp();
	    }
	    public function testGetAttributes()
	    {
	        $story = new Story();
	        $story->story_id = '23';
	        $story->title = 'Test Story';
	        $story->link = 'http://www.google.com';
	        $story->description = 'this is a test story.';
	        $story->story_date = '9/22/1984';
	        $story->story_size = 'MEDIUM';
	        $story->show_desc = true;
	        $story->visible = 1;
	        $expected = 
	        $this->assertEquals(array(
	            'title' => 'Test Story',
	            'description' => 'this is a test story.'
	        ), $story->getAttributes());
	        $this->assertEquals(array(
	            'title' => 'Test Story',
	            'description' => 'this is a test story.'
	        ), $story->getAttributes(array('title', 'description')));
	        $this->assertEquals(array(
	            'title' => 'Test Story',
	            'description' => 'this is a test story.'
	        ), $story->getAttributes(null, array('customLabel', 'underscore_style')));
	        $this->assertEquals(array(
	            'title' => 'Test Story'
	        ), $story->getAttributes(array('title', 'description'), array('description', 'customLabel', 'underscore_style')));
	    }
	}
?>