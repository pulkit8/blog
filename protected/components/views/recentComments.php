


<ul>
        <?php foreach($this->getRecentComments() as $comment): ?>
        <li>
                <?php echo CHtml::link(CHtml::encode($comment->post->title),$comment->getURL()); ?>
        </li>
        <?php endforeach; ?>
</ul>