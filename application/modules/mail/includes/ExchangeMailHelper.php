<?php
/**
 * Вспомогательный класс для отправки сообщения на Exchange-сервер (Outlook Web App).
 * При такой отправке запоминаются исходящие сообщения.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2015
 * @package application.modules.mail.includes
 */
class ExchangeMailHelper
{
	/**
	 * Отпправляет сообщение.
	 * @param MailForm $model Модель для отправки формы почтового сообщения.
	 * @param integer $type Тип сообщения.
	 * @return boolean Успешность отправки сообщения.
	 */
	public static function send($model, $type)
	{
		if ($model->files)
			return self::sendWithAttachment($model, $type);
		else
			return self::sendWithoutAttachment($model, $type);
	}


	/**
	 * Отпправляет сообщение без прикрепленных файлов.
	 * @param MailForm $model Модель для отправки формы почтового сообщения.
	 * @param integer $type Тип сообщения.
	 * @return boolean Успешность отправки сообщения.
	 */
	protected static function sendWithoutAttachment($model, $type)
	{
		$user = Yii::app()->user->param;
		
		Yii::import('application.modules.mail.extensions.php-ews.ExchangeWebServices', true);
		Yii::import('application.modules.mail.extensions.php-ews.NTLMSoapClient', true);
		Yii::import('application.modules.mail.extensions.php-ews.NTLMSoapClient.Exchange', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWS_Exception', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType', true);
		
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.MessageType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.EmailAddressType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.BodyType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.SingleRecipientType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.CreateItemType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ArrayOfRecipientsType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.NonEmptyArrayOfAllItemsType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ItemType', true);
		
		$server   = $user['email_host'];
		$username = $user['email_username'];
		$password = $user['email_password'];
		
		$ews = new ExchangeWebServices($server, $username, $password);

		$msg = new EWSType_MessageType();
		
		$toAddresses = array();
		foreach($model->emails as $email) {
			$toAddress = new EWSType_EmailAddressType();
			$toAddress->EmailAddress = $email;
			$toAddresses[] = $toAddress;
		}
		$msg->ToRecipients = $toAddresses;
		
		$toAddresses = array();
		foreach($model->cc as $cc) {
			$toAddress = new EWSType_EmailAddressType();
			$toAddress->EmailAddress = $cc;
			$toAddresses[] = $toAddress;
		}
		$msg->CcRecipients = $toAddresses;
		
		$fromAddress = new EWSType_EmailAddressType();
		$fromAddress->EmailAddress = $user['email_address'];
		$fromAddress->Name = $user['email_from_name'];

		$msg->From = new EWSType_SingleRecipientType();
		$msg->From->Mailbox = $fromAddress;
		
		$msg->Subject = $model->subject;
		
		$msg->Body = new EWSType_BodyType();
		$msg->Body->BodyType = 'HTML';
		$msg->Body->_ = $model->text;
		
		$msgRequest = new EWSType_CreateItemType();
		$msgRequest->Items = new EWSType_NonEmptyArrayOfAllItemsType();
		$msgRequest->Items->Message = $msg;
		$msgRequest->MessageDisposition = 'SendAndSaveCopy';
		$msgRequest->MessageDispositionSpecified = true;
				
		$response = $ews->CreateItem($msgRequest);
		
		return ($response->ResponseMessages->CreateItemResponseMessage->ResponseClass == 'Success');
	}


	/**
	 * Отпправляет сообщение с прикрепленными файлами.
	 * @param MailForm $model Модель для отправки формы почтового сообщения.
	 * @param integer $type Тип сообщения.
	 * @return boolean Успешность отправки сообщения.
	 */
	protected static function sendWithAttachment($model, $type)
	{
		$user = Yii::app()->user->param;
		
		Yii::import('application.modules.mail.extensions.php-ews.ExchangeWebServices', true);
		Yii::import('application.modules.mail.extensions.php-ews.NTLMSoapClient', true);
		Yii::import('application.modules.mail.extensions.php-ews.NTLMSoapClient.Exchange', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWS_Exception', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType', true);
		
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.MessageType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.EmailAddressType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.BodyType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.SingleRecipientType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.CreateItemType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ArrayOfRecipientsType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.NonEmptyArrayOfAllItemsType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ItemType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ExchangeImpersonationType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ConnectingSIDType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.FileAttachmentType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.CreateAttachmentType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.SendItemType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.ItemIdType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.TargetFolderIdType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.DistinguishedFolderIdType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.NonEmptyArrayOfAttachmentsType', true);
		Yii::import('application.modules.mail.extensions.php-ews.EWSType.NonEmptyArrayOfBaseItemIdsType', true);
		
		$server   = $user['email_host'];
		$username = $user['email_username'];
		$password = $user['email_password'];
		
		$ews = new ExchangeWebServices($server, $username, $password);
		
		// Create message
		$msg = new EWSType_MessageType();
		$toAddresses = array();
		foreach($model->emails as $email) {
			$toAddress = new EWSType_EmailAddressType();
			$toAddress->EmailAddress = $email;
			$toAddresses[] = $toAddress;
		}
		$msg->ToRecipients = $toAddresses;
		
		$toAddresses = array();
		foreach($model->cc as $cc) {
			$toAddress = new EWSType_EmailAddressType();
			$toAddress->EmailAddress = $cc;
			$toAddresses[] = $toAddress;
		}
		$msg->CcRecipients = $toAddresses;
		
		$fromAddress = new EWSType_EmailAddressType();
		$fromAddress->EmailAddress = $user['email_address'];
		$fromAddress->Name = $user['email_from_name'];
		
		$msg->From = new EWSType_SingleRecipientType();
		$msg->From->Mailbox = $fromAddress;
		
		$msg->Subject = $model->subject;
		
		$msg->Body = new EWSType_BodyType();
		$msg->Body->BodyType = 'HTML';
		$msg->Body->_ = $model->text;
		
		// Save message
		$msgRequest = new EWSType_CreateItemType();
		$msgRequest->Items = new EWSType_NonEmptyArrayOfAllItemsType();
		$msgRequest->Items->Message = $msg;
		$msgRequest->MessageDisposition = 'SaveOnly';
		$msgRequest->MessageDispositionSpecified = true;
		
		$msgResponse = $ews->CreateItem($msgRequest);
		$msgResponseItems = $msgResponse->ResponseMessages->CreateItemResponseMessage->Items;
		
		// Create attachment(s)
		$attachments = array();
		foreach($model->files as $file) {
			$attachment = new EWSType_FileAttachmentType();
			$attachment->Content = $file['content'];
			$attachment->Name = $file['name'];
			$attachments[] = $attachment;
		}
		
		// Attach files to message
		$attRequest = new EWSType_CreateAttachmentType();
		$attRequest->ParentItemId = $msgResponseItems->Message->ItemId;
                $attRequest->Attachments = new EWSType_NonEmptyArrayOfAttachmentsType();
		$attRequest->Attachments->FileAttachment = $attachments;
		
		$attResponse = $ews->CreateAttachment($attRequest);
		$attResponseId = $attResponse->ResponseMessages->CreateAttachmentResponseMessage->Attachments->FileAttachment->AttachmentId;
		
		// Save message id from create attachment response
		$msgItemId = new EWSType_ItemIdType();
		$msgItemId->ChangeKey = $attResponseId->RootItemChangeKey;
		$msgItemId->Id = $attResponseId->RootItemId;
		
		// Send and save message
		$msgSendRequest = new EWSType_SendItemType();
                $msgSendRequest->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
		$msgSendRequest->ItemIds->ItemId = $msgItemId;
		$msgSendRequest->SavedItemFolderId = new EWSType_TargetFolderIdType();
		$sentItemsFolder = new EWSType_DistinguishedFolderIdType();
		$sentItemsFolder->Id = 'sentitems';
		$msgSendRequest->SavedItemFolderId->DistinguishedFolderId = $sentItemsFolder;
		$msgSendRequest->SaveItemToFolder = true;
		$msgSendResponse = $ews->SendItem($msgSendRequest);
		
		return ($msgSendResponse->ResponseMessages->SendItemResponseMessage->ResponseClass == 'Success');
	}
}