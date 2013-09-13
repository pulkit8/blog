<?php

/**
 * This is the model class for table "{{post}}".
 *
 * The followings are the available columns in table '{{post}}':
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property string $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $author_id
 * @property integer $id
 */
class Post extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        const STATUS_DRAFT=1;
        const STATUS_PUBLISHED=2;
        const STATUS_ARCHIVED=3;
   
        public function getUrl()
        {
               return Yii::app()->createUrl('post/view', array(
               'id'=>$this->id,'title'=>$this->title,));
        }
        
	public function tableName()
	{
		return '{{post}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
	    return array(
                        array('title, content, status', 'required'),
                        array('title', 'length', 'max'=>128),
                        array('status', 'in', 'range'=>array(1,2,3)),
                        array('tags', 'match', 'pattern'=>'/^[\w\s,]+$/',
                        'message'=>'Tags can only contain word characters.'),
                        array('tags', 'normalizeTags'),
                        array('title, status', 'safe', 'on'=>'search'),
                        );
        /*    return array(
			array('title, content, tags, status, create_time, update_time, author_id', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('title, content, tags, status, create_time, update_time', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('title, content, tags, status, create_time, update_time, author_id, id', 'safe', 'on'=>'search'),
		);*/
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
 
             return array(
                'author' => array(self::BELONGS_TO, 'User', 'author_id'),
                'comments' => array(self::HAS_MANY, 'Comment', 'post_id',
                'condition'=>'comments.status='.Comment::STATUS_APPROVED,
                'order'=>'comments.create_time DESC'),
                'commentCount' => array(self::STAT, 'Comment', 'post_id',
                'condition'=>'status='.Comment::STATUS_APPROVED),
            );

                // NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
        
        public function normalizeTags($attribute,$params)
        {
        $this->tags=Tag::array2string(array_unique(Tag::string2array($this->tags)));
        }
        
        public function attributeLabels()
	{
		return array(
			'title' => 'Title',
			'content' => 'Content',
			'tags' => 'Tags',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'author_id' => 'Author',
			'id' => 'ID',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('id',$this->id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Post the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        protected function beforeSave()
        {
            if(parent::beforeSave())
        {
            if($this->isNewRecord)
        {
            $this->create_time=$this->update_time= new CDbExpression("NOW()");
            $this->author_id=Yii::app()->user->id;
        }
        else
            $this->update_time= new CDbExpression("NOW()");
        return true;
        }
        else
        return false;
        }
        
        protected function afterSave()
        {
            parent::afterSave();
            Tag::model()->updateFrequency($this->_oldTags, $this->tags);
        }
 
        private $_oldTags;
 
        protected function afterFind()
        {
            parent::afterFind();
            $this->_oldTags=$this->tags;
        }
        public function addComment($comment)
        {
            if(Yii::app()->params['commentNeedApproval'])
            $comment->status=Comment::STATUS_PENDING;
        else
            $comment->status=Comment::STATUS_APPROVED;
            $comment->post_id=$this->id;
        return $comment->save();
        }
        protected function afterDelete()
        {
            parent::afterDelete();
            Comment::model()->deleteAll('post_id='.$this->id);
            Tag::model()->updateFrequency($this->tags, '');
        }
        
}
