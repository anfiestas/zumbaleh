<?php
require_once 'Objects/MessageBroadcast.php';
class IMessageBroadcastRepresentation {
    
    public $toUserId;     
    public $fromUserId;
    public $toPhone;
    public $fromPhone;
    public $text;
    public $timestamp;
    public $status;
    
}
?>