<?php
namespace devmx\Teamspeak3;

/**
 * Channeldata
 * @author drak3
 */
class Channel implements devmx\Teamspeak3\Node\ChannelInterface
{
    protected $codec;
    protected $codecQuality;
    protected $description;
    protected $filePath;
    
    protected $clients = Array();
    
    public function getClients( $predicate = NULL )
    {
        if(!is_callable( $predicate)) {
            return $this->clients;
        }
        return $this->findClient($predicate);
    }
    
    public function findClients($predicate) {
        
    }

    public function getCodec()
    {
        return $this->codec;
    }

    public function getCodecQuality()
    {
        return $this->codecQuality;
    }

    public function getDescription()
    {
        
    }

    public function getFilepath()
    {
        
    }

    public function getHasMaxClientsLimit()
    {
        
    }

    public function getHasMaxFamilyClientsLimit()
    {
        
    }

    public function getHasPassword()
    {
        
    }

    public function getIconId()
    {
        
    }

    public function getId()
    {
        
    }

    public function getMaxClients()
    {
        
    }

    public function getMaxFamilyClients()
    {
        
    }

    public function getName()
    {
        
    }

    public function getNeededTalkPower()
    {
        
    }

    public function getParentId()
    {
        
    }

    public function getPassword()
    {
        
    }

    public function getPhoneticName()
    {
        
    }

    public function getPosition()
    {
        
    }

    public function getTopic()
    {
        
    }

    public function isDefault()
    {
        
    }

    public function isInheritingMaxFamilyClients()
    {
        
    }

    public function isPermanent()
    {
        
    }

    public function isSemiPermanent()
    {
        
    }

    public function isSilenced()
    {
        
    }

    public function isSpeechDataEncrypted()
    {
        
    }

    public function isTemporary()
    {
        
    }

    public function move( $toParent )
    {
        
    }

    public function sendMessage( $msg )
    {
        
    }

    public function setCodec( $Codec )
    {
        
    }

    public function setCodecQuality( $CodecQuality )
    {
        
    }

    public function setDescription( $Description )
    {
        
    }

    public function setFilepath( $filepath )
    {
        
    }

    public function setHasMaxClientsLimit( $hasMaxClientsLimit )
    {
        
    }

    public function setHasMaxFamilyClientsLimit( $hasMaxFamilyClientsLimit )
    {
        
    }

    public function setHasPassword( $hasPassword )
    {
        
    }

    public function setIconId( $iconId )
    {
        
    }

    public function setId( $Id )
    {
        
    }

    public function setIsDefault( $isDefault )
    {
        
    }

    public function setIsInheritingMaxFamilyClients( $isInheritingMaxFamilyClients )
    {
        
    }

    public function setIsPermanent( $isPermanent )
    {
        
    }

    public function setIsSemiPermanent( $isSemiPermanent )
    {
        
    }

    public function setIsSilenced( $isSilenced )
    {
        
    }

    public function setIsSpeechDataEncrypted( $isSpeechDataEncrypted )
    {
        
    }

    public function setIsTemporary( $isTemporary )
    {
        
    }

    public function setMaxClients( $maxClients )
    {
        
    }

    public function setMaxFamilyClients( $maxFamilyClients )
    {
        
    }

    public function setName( $Name )
    {
        
    }

    public function setNeededTalkPower( $neededTalkPower )
    {
        
    }

    public function setParentId( $ParentId )
    {
        
    }

    public function setPassword( $Password )
    {
        
    }

    public function setPhoneticName( $phoneticName )
    {
        
    }

    public function setPosition( $Position )
    {
        
    }

    public function setTopic( $Topic )
    {
        
    }

    
    
}

?>
