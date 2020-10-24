<?php

namespace app\models\base;

/**
 * This is the base model class for table "band".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property integer $user_id
 * @property string $logo
 * @property string $lno_score
 * @property string $type
 * @property string $genre
 * @property string $similar_to
 * @property string $hometown_city
 * @property string $hometown_state
 * @property string $description
 * @property string $website
 * @property string $youtube
 * @property string $instagram
 * @property string $facebook
 * @property string $twitter
 * @property string $source
 * @property array $attr
 * @property string $scrape_status
 * @property string $gg_last_attempt
 * @property string $ytlink_first
 * @property string $ytlink_first_tnail
 * @property integer $ytlink_approved
 *
 * @property \app\models\User $user
 * @property \app\models\BandComment[] $bandComments
 * @property \app\models\BandEvent[] $bandEvents
 * @property \app\models\BandFollow[] $bandFollows
 * @property \app\models\BandRate[] $bandRates
 * @property \app\models\Bvideo[] $bvideos
 */
class Band extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'user',
            'bandComments',
            'bandEvents',
            'bandFollows',
            'bandRates',
            'bvideos'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'gg_last_attempt'], 'safe'],
            [['user_id','ytlink_approved'], 'integer'],
            [['lno_score'], 'number'],
            [['type', 'description', 'source', 'attr', 'scrape_status'], 'string'],
            [['name', 'hometown_city'], 'string', 'max' => 100],
            [['logo'], 'string', 'max' => 300],
            [['genre', 'website', 'facebook', 'twitter'], 'string', 'max' => 255],
            [['similar_to'], 'string', 'max' => 5000],
            [['hometown_state'], 'string', 'max' => 50],
            [['youtube', 'ytlink_first'], 'string', 'max' => 800],
            [['instagram', 'ytlink_first_tnail'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'band';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
            'logo' => 'Logo',
            'lno_score' => 'Lno Score',
            'type' => 'Type',
            'genre' => 'Genre',
            'similar_to' => 'Similar To',
            'hometown_city' => 'Hometown City',
            'hometown_state' => 'Hometown State',
            'description' => 'Description',
            'website' => 'Website',
            'youtube' => 'Youtube',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'source' => 'Source',
            'attr' => 'Attr',
            'scrape_status' => 'Scrape Status',
            'gg_last_attempt' => 'Gg Last Attempt',
            'ytlink_first' => 'First Youtube video id',
            'ytlink_first_tnail' => 'First Youtube video thumnail',
            'ytlink_approved' => 'Approve YT vid?',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'user_id'])->inverseOf('bands');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBandComments()
    {
        return $this->hasMany(\app\models\BandComment::className(), ['band_id' => 'id'])->inverseOf('band');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBandEvents()
    {
        return $this->hasMany(\app\models\BandEvent::className(), ['band_id' => 'id'])->inverseOf('band');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBandFollows()
    {
        return $this->hasMany(\app\models\BandFollow::className(), ['band_id' => 'id'])->inverseOf('band');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBandRates()
    {
        return $this->hasMany(\app\models\BandRate::className(), ['band_id' => 'id'])->inverseOf('band');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBvideos()
    {
        return $this->hasMany(\app\models\Bvideo::className(), ['band_id' => 'id'])->inverseOf('band');
    }
    }
