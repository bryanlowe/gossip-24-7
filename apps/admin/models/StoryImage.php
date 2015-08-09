<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;
	use yii\web\UploadedFile;

	class StoryImage extends ActiveRecord {
		public $imageFile;
		const SCENARIO_STORY_IMAGE = 'story_image';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_IMAGE] = ['story_image_id', 'story_id', 'image_name', 'order'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_image';
	    }

	    /**
	     * Creates a relation between story and story image tables
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
	            [['story_id', 'image_name'], 'required', 'on' => self::SCENARIO_STORY_IMAGE],
	            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, bmp']
	        ];
	    }

	    /**
	     * Uploads the image to the server
	     */
	    public function upload(){
	        if($this->validate()){ 
	        	$targetPath = '/home/gossip24/public_html/uploads/story/' . $this->story_id . '/images/';
				if(!file_exists($targetPath)){
					mkdir($targetPath, 0755, true);
				}
	            $this->imageFile->saveAs($targetPath . $this->image_name);
	            return true;
	        }
	        return false;
	    }
	}
?>