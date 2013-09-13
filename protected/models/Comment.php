<?php

/**
 * This is the model class for table "{{comment}}".
 *
 * The followings are the available columns in table '{{comment}}':
 * @property string $content
 * @property string $status
 * @property string $create_time
 * @property string $author
 * @property string $email
 * @property string $url
 * @property integer $post_id
 * @property integer $id
 */
class Comment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
    
        public $content;
        const STATUS_PENDING=1;
        const STATUS_APPROVED=2;
	public function tableName()
	{
		return '{{comment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
        public function rules()
        {
            return array(
                array('content, author, email', 'required'),
                array('author, email, url', 'length', 'max'=>128),
                array('email','email'),
                array('url','url'),
                        )
        ;}
/*	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, status, create_time, author, email, url, post_id', 'required'),
			array('post_id', 'numerical', 'integerOnly'=>true),
			array('content, status, create_time, author, email, url', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('content, status, create_time, author, email, url, post_id, id', 'safe', 'on'=>'search'),
		);
	} */

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
                  return array(
                  'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
                  );
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'content' => 'Content',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'author' => 'Author',
			'email' => 'Email',
			'url' => 'Url',
			'post_id' => 'Post',
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

		$criteria->compare('content',$this->content,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('post_id',$this->post_id);
		$criteria->compare('id',$this->id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
            $this->create_time=time();
            return true;
        }
        else
        return false;
}
        protected function newComment($post)
        {
            $comment=new Comment;
 
            if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
            {
                echo CActiveForm::validate($comment);
                Yii::app()->end();
            }
 
            if(isset($_POST['Comment']))
            {
                $comment->attributes=$_POST['Comment'];
                if($post->addComment($comment))
                {
                    if($comment->status==Comment::STATUS_PENDING)
                    Yii::app()->user->setFlash('commentSubmitted','Thank you for your comment. Your comment will be posted once it is approved.');
                    $this->refresh();
                }
            }
            return $comment;
        }
        
        public function approve()
        {
            $this->status=Comment::STATUS_APPROVED;
            $this->update(array('status'));
        }
        
        public function getPendingCommentCount()
        {
            $criteria = new CDbCriteria; $criteria->condition='status = 0';
            return Comment::model()->count($criteria);
        }
       
        public function getUrl($post=null)
        {
            if($post===null)
            $post=$this->post;
            return $post->url.'#c'.$this->id;
        }
        
        public function findRecentComments($limit=10)
        {
            return $this->with('post')->findAll(array(
            'condition'=>'t.status='.self::STATUS_APPROVED,
            'order'=>'t.create_time DESC',
            'limit'=>$limit,
            ));
        }
}
