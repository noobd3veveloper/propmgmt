<?php

namespace app\models;

use Yii;
use app\vendor\bcrypt;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "user".
 *
 * @property int $userID ID
 * @property string $username User Name
 * @property string $userPassword Password
 * @property string $userAuthKey Authorization Key
 * @property string $userAccessToken Access Token
 * @property string $userEmail E-mail
 * @property string $userGivenName Given Name
 * @property string $userSurname Surname
 * @property int $createdBy
 * @property int $createdDate
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'userEmail', 'userGivenName', 'userSurname',  'roleID', 'startdate'], 'required'],
            [['username'], 'unique','message'=>'Username already exists'],
            [['createdBy', 'createdDate', 'duration'], 'integer'],
            [['startdate'],'safe'],
            [['username', 'userPassword', 'userAuthKey', 'userAccessToken', 'userEmail', 'userGivenName', 'userSurname','userResetToken', 'companyName'], 'string', 'max' => 255],
            [['roleID'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['roleID' => 'roleID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userID' => 'ID',
            'username' => 'User Name',
            'userPassword' => 'Password',
            'userAuthKey' => 'Authorization Key',
            'userAccessToken' => 'Access Token',
            'userEmail' => 'E-mail',
            'userGivenName' => 'Given Name',
            'userSurname' => 'Surname',
            'userResetToken' => 'Reset Token',
            'companyName' => 'Company Name',
            'duration' => 'Subscription (Days : 0 for lifetime)',
            'startdate' => 'Subscription Start Date',
            'roleID' => 'Role',
            'createdBy' => 'Created By',
            'createdDate' => 'Created Date',
        ];
    }

    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['roleID' => 'roleID']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getAuthKey()
    {
        return $this->userAuthKey;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getFullName()
	{
		$fullname = $this->userGivenName; 
		if($this->userSurname != '') { 
			$fullname .= ' '.$this->userSurname;    
		} 
		return $fullname; 
    } 

    public function getDaysLeft()
	{
		$date1=date_create( date("Y-m-d"));
        $date2=date_create($this->startdate);
        $diff=date_diff($date1,$date2);
        return $diff;
    } 

   

    public function getFullNameWithCompany()
	{
		$fullname = $this->userGivenName; 
		if($this->userSurname != '') { 
			$fullname .= ' '.$this->userSurname;    
		} 
		return $fullname . ' of ' . $this->companyName; 
    }
    
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function removePasswordResetToken()
    {
        $this->userResetToken = null;
    }

    public function validatePassword($password)
    {
        return bCrypt::verify($password, $this->userPassword);
    }

    public function hashPassword($password)
	{
        $enc = NEW bCrypt();
        return $enc->hash($password);
		//return Yii::$app->getSecurity()->generatePasswordHash($password);
    }
    
    public function generatePasswordResetToken()
    {
        $this->userResetToken = Security::generateRandomKey() . '_' . time();
    }

    public function setPassword($password)
    {
        $this->password_hash = $password;//Security::generatePasswordHash($password);
    }

    public function validateSubscription($id){
        
        $sql = 'SELECT DATEDIFF(\'2018-03-20\',startdate) runningdays
                      ,duration
                FROM user 
                WHERE userID = :id';
        $connection = Yii::$app->getDb();
        $result = $connection->createCommand($sql)->bindValue('id',$id)->queryAll(); 
        //var_dump($result);
        return $result;
    }
    public function getCreatedBy0()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
}
