<?php
/**
 * @files  array  - Список прикрепленных файлов.
 */
?>
<?php
	$result = '';
	foreach($files as $file) {
		$result .=
			'<tr>
				<td>
					'.CHtml::link($file['name'], array('file', 'id'=>$file['id'])).'&nbsp;&nbsp;&nbsp;
				</td>
				<td>
				'.(
					(isset(BooksFiles::$is_image[pathinfo($file['name'], PATHINFO_EXTENSION)])) ?
						CHtml::link(' (просмотр)', array('viewFile', 'id'=>$file['id']), array('target'=>'_blank'))
						: ''
				).'&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					'.CHtml::link('удалить', array('removeFile', 'id'=>$file['id']), array(
						'style'        => 'color:red;',
						'data_id'      => $file['id'],
						'book_id'      => $file['book_id'],
						'class'        => BooksFiles::className().'-file-delete',
					)).'&nbsp;&nbsp;&nbsp;
				</td>
			</tr>';
	}
	
	echo $result;