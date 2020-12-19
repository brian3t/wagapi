<?php

namespace app\models\base;

/**
 * This is the base model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $unconfirmed_email
 * @property string $registration_ip
 * @property integer $flags
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $updated_at
 * @property integer $created_at
 * @property integer $last_login_at
 * @property string $last_login_ip
 * @property string $auth_tf_key
 * @property integer $auth_tf_enabled
 * @property integer $password_changed_at
 * @property integer $gdpr_consent
 * @property integer $gdpr_consent_date
 * @property integer $gdpr_deleted
 * @property integer $point
 *
 * @property \app\models\Invi[] $invis
 * @property \app\models\Profile $profile
 * @property \app\models\SocialAccount[] $socialAccounts
 * @property \app\models\Token[] $tokens
 * @property \app\models\Wager[] $wagers
 */
class User extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /*public function getLinks()
    {
        $reflect = new \ReflectionClass($this);
        $controller = Inflector::camel2id($reflect->getShortName());
        return [
            Link::REL_SELF => Url::to(["$controller/view", 'id' => $this->getId()], true)
        ];
    }*/


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'invis',
            'profile',
            'socialAccounts',
            'tokens',
            'wagers'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password_hash', 'auth_key', 'updated_at', 'created_at'], 'required'],
            [['flags', 'confirmed_at', 'blocked_at', 'updated_at', 'created_at', 'last_login_at', 'password_changed_at', 'gdpr_consent_date', 'point'], 'integer'],
            [['username', 'email', 'unconfirmed_email'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['registration_ip', 'last_login_ip'], 'string', 'max' => 45],
            [['auth_tf_key'], 'string', 'max' => 16],
            [['auth_tf_enabled', 'gdpr_consent', 'gdpr_deleted'], 'boolean'],
            [['username'], 'unique'],
            [['email'], 'unique']
        ];
    }

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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'unconfirmed_email' => 'Unconfirmed Email',
            'registration_ip' => 'Registration Ip',
            'flags' => 'Flags',
            'confirmed_at' => 'Confirmed At',
            'blocked_at' => 'Blocked At',
            'last_login_at' => 'Last Login At',
            'last_login_ip' => 'Last Login Ip',
            'auth_tf_key' => 'Auth Tf Key',
            'auth_tf_enabled' => 'Auth Tf Enabled',
            'password_changed_at' => 'Password Changed At',
            'gdpr_consent' => 'Gdpr Consent',
            'gdpr_consent_date' => 'Gdpr Consent Date',
            'gdpr_deleted' => 'Gdpr Deleted',
            'point' => 'Point',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvis()
    {
        return $this->hasMany(\app\models\Invi::className(), ['invited_user_id' => 'id'])->inverseOf('invitedUser');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(\app\models\Profile::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialAccounts()
    {
        return $this->hasMany(\app\models\SocialAccount::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(\app\models\Token::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagers()
    {
        return $this->hasMany(\app\models\Wager::className(), ['pending_by' => 'id'])->inverseOf('acceptedBy')->inverseOf('createdBy')->inverseOf('pendingBy');
    }
    }
