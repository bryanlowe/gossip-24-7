<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;
	use yii\web\UploadedFile;

	class StoryAudio extends ActiveRecord {
		public $audioFile;
		const SCENARIO_STORY_AUDIO = 'story_audio';

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_STORY_AUDIO] = ['story_audio_id', 'story_id', 'audio_name'];
	        return $scenarios;
	    }

	    /**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'story_audio';
	    }

	    /**
	     * Creates a relation between story and story audio tables
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
	            [['story_id', 'audio_name'], 'required', 'on' => self::SCENARIO_STORY_AUDIO],
	            [['audioFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp3, wav, mpeg, wma, webm']
	        ];
	    }

	    /**
	     * Uploads the audio to the server
	     */
	    public function upload(){
	        if($this->validate()){ 
	        	$targetPath = '/home/gossip24/public_html/uploads/story/' . $this->story_id . '/audio/';
				if(!file_exists($targetPath)){
					mkdir($targetPath, 0755, true);
				}
	            $this->audioFile->saveAs($targetPath . $this->audio_name);
	            return true;
	        }
	        return false;
	    }
	}
?>