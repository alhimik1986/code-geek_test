<?php $onClick = "jQuery(this).parents('.pager-links').parent().parent().parent().parent().find('input[name=\"".$pageName."\"]').val(jQuery(this).attr('page'));"; ?>
	<div class="pager-info">
		<?php echo Yii::t('app', 'Rows:'); ?> <?php echo $pagerInfo['start']; ?> - <?php echo $pagerInfo['stop']; ?> <?php echo Yii::t('app', 'of'); ?> <?php echo $pagerInfo['count']; ?>
	</div>
<?php if ($pagerInfo['pagesCount'] > 1 ): ?>
	<div class="pager-links">
		<?php echo CHtml::hiddenField($pageName, $pagerInfo['page']); ?>
		
		<?php if ($pagerInfo['prevPage']): ?>
			<?php echo CHtml::link(Yii::t('app', 'Previous'), array('index', 'limit'=>$pagerInfo['limit'], 'page'=>$pagerInfo['prevPage']), array('page'=>$pagerInfo['prevPage'], 'onclick'=>$onClick)); ?>
		<?php else: ?>
			<span class="pager-current-page"><?php echo Yii::t('app', 'Previous'); ?></span>
		<?php endIf; ?>

		<?php if ($pagerInfo['left']): ?>
			<?php foreach($pagerInfo['left'] as $page): ?>
				<?php echo CHtml::link($page, array('index', 'limit'=>$pagerInfo['limit'], 'page'=>$page), array('page'=>$page, 'onclick'=>$onClick)); ?>
			<?php endForeach; ?>...
		<?php endIf; ?>

		<?php if ($pagerInfo['middle']): ?>
			<?php foreach($pagerInfo['middle'] as $page): ?>
				<?php if ($page != $pagerInfo['page']): ?>
					<?php echo CHtml::link($page, array('index', 'limit'=>$pagerInfo['limit'], 'page'=>$page), array('page'=>$page, 'onclick'=>$onClick)); ?>
				<?php else: ?>
					<span class="pager-current-page"><?php echo $page; ?></span>
				<?php endIf; ?>
			<?php endForeach; ?>
		<?php endIf; ?>

		<?php if ($pagerInfo['right']): ?>
			...<?php foreach($pagerInfo['right'] as $page): ?>
				<?php echo CHtml::link($page, array('index', 'limit'=>$pagerInfo['limit'], 'page'=>$page), array('page'=>$page, 'onclick'=>$onClick)); ?>
			<?php endForeach; ?>
		<?php endIf; ?>

		<?php if ($pagerInfo['nextPage']): ?>
			<?php echo CHtml::link(Yii::t('app', 'Next'), array('index', 'limit'=>$pagerInfo['limit'], 'page'=>$pagerInfo['nextPage']), array('page'=>$pagerInfo['nextPage'], 'onclick'=>$onClick)); ?>
		<?php else: ?>
			<span class="pager-current-page"><?php echo Yii::t('app', 'Next'); ?></span>
		<?php endIf; ?>
	</div>
	
	<div style="clear:both"></div>
<?php endIf; ?>