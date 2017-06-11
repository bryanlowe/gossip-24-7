<?php
	namespace app\models;

	use Yii;
	use yii\base\NotSupportedException;
	use yii\db\ActiveRecord;
	use yii\helpers\Security;
	use \yii\web\IdentityInterface;

	class User extends ActiveRecord implements IdentityInterface{
		const SCENARIO_USER = 'user';
		const SCENARIO_LOGIN = 'login';

		public $id;
		public $authKey;
		public $accessToken;

		/**
	     * @return string the name of the table associated with this ActiveRecord class.
	     */
	    public static function tableName(){
	        return 'user';
	    }	    

	    /**
	     * @return array the scenarios.
	     */
	    public function scenarios() {
	        $scenarios = parent::scenarios();
	        $scenarios[self::SCENARIO_USER] = ['user_id', 'first_name', 'last_name', 'username', 'password', 'email', 'active_status'];
	        $scenarios[self::SCENARIO_LOGIN] = ['username', 'password'];
	        return $scenarios;
	    }

	    /**
	     * @return array the validation rules.
	     */
	    public function rules() {
	        return [
	            // required
	            [['first_name', 'last_name', 'username', 'password', 'email', 'active_status'], 'required', 'on' => self::SCENARIO_USER],
	            [['username', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],
	            [['username', 'password'], 'string', 'max' => 100, 'on' => self::SCENARIO_LOGIN]
	        ];
	    }

		/**
		 * @inheritdoc
		 */
		public static function findIdentity($id)
	    {
	        return static::findOne($id);
	    }

		/**
		 * @inheritdoc
		 */
		public static function findIdentityByAccessToken($token, $type = null)
	    {
	          return static::findOne(['access_token' => $token]);
	    }

		/**
		 * Finds user by username
		 *
		 * @param  string      $username
		 * @return static|null
		 */
		public static function findByUsername($username)
	    {	
	        return static::findOne(['username' => $username]);
	    }

	    /**
	     * Finds user by password reset token
	     *
	     * @param  string      $token password reset token
	     * @return static|null
	     */
	    public static function findByPasswordResetToken($token)
	    {
	   	    $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
	        $parts = explode('_', $token);
	        $timestamp = (int) end($parts);
	        if ($timestamp + $expire < time()) {
	            // token expired
	            return null;
	        }
	 
	        return static::findOne([
	            'password_reset_token' => $token
	        ]);
	    }

		/**
	     * @inheritdoc
	     */
	    public function getId()
	    {
	        return $this->getPrimaryKey();
	    }
	 
	    /**
	     * @inheritdoc
	     */
	    public function getAuthKey()
	    {
	        return $this->auth_key;
	    }
	 
	    /**
	     * @inheritdoc
	     */
	    public function validateAuthKey($authKey)
	    {
	        return $this->getAuthKey() === $authKey;
	    }
	 
	    /**
	     * Validates password
	     *
	     * @param  string  $password password to validate
	     * @return boolean if password provided is valid for current user
	     */
	    public function validatePassword($password)
	    {
	        return $this->password === sha1($password);
	    }
	 
	    /**
	     * Generates password hash from password and sets it to the model
	     *
	     * @param string $password
	     */
	    public function setPassword($password)
	    {
	        $this->password_hash = Security::generatePasswordHash($password);
	    }
	 
	    /**
	     * Generates "remember me" authentication key
	     */
	    public function generateAuthKey()
	    {
	        $this->auth_key = Security::generateRandomKey();
	    }

	    /**
	     * Generates new password reset token
	     */
	    public function generatePasswordResetToken()
	    {
	        $this->password_reset_token = Security::generateRandomKey() . '_' . time();
	    }
	 
	    /**
	     * Removes password reset token
	     */
	    public function removePasswordResetToken()
	    {
	        $this->password_reset_token = null;
	    }
	}
?>