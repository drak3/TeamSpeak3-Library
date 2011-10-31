<?php

/*
  This file is part of TeamSpeak3 Library.

  TeamSpeak3 Library is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  TeamSpeak3 Library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with TeamSpeak3 Library. If not, see <http://www.gnu.org/licenses/>.
 */
declare(encoding = "UTF-8");

namespace devmx\Teamspeak3;

/**
 * @since 1.0
 * @author drak3, Maximilian Narr
 */
interface ChannelInterface
{

    // speex narrowband (mono, 16bit, 8kHz)
    const CODEC_SPEEX_NARROWBAND = 0;
    
    // speex wideband (mono, 16bit, 16kHz)
    const CODEC_SPEEX_WIDEBAND = 1;
    
    // speex ultra-wideband (mono, 16bit, 32kHz)
    const CODEC_SPEEX_ULTRAWIDEBAND = 2;
    
    // celt mono (mono, 16bit, 48kHz)
    const CODEC_CELT_MONO = 3;

    
    
    /**
     * @since 1.0
     * @return Channel name 
     */
    public function getChannelName();

    /**
     * @since 1.0
     * @param string $channelName Channel name 
     */
    public function setChannelName($channelName);

    
    /**
     * @since 1.0
     * @return Channel Topic 
     */
    public function getChannelTopic();

    /**
     * @since 1.0
     * @param string $channelTopic Channel topic 
     */
    public function setChannelTopic($channelTopic);

    
    /**
     * @since 1.0
     * @return string Channel description 
     */
    public function getChannelDescription();

    /**
     * @since 1.0
     * @param string $channelDescription Channel description 
     */
    public function setChannelDescription($channelDescription);

    
    /**
     * @since 1.0
     * @return Channel password 
     */
    public function getChannelPassword();

    /**
     * @since 1.0
     * @param string $channelPassword Channel password 
     */
    public function setChannelPassword($channelPassword);

    
    /**
     * @since 1.0
     * @return bool If the channel has a password 
     */
    public function getHasChannelPassword();

    /**
     * @since 1.0 
     * @param bool $hasChannelPassword If the channel has a password
     */
    public function setHasChannelPassword($hasChannelPassword);

    
    /**
     * @since 1.0
     * @return int Channel codec 
     */
    public function getChannelCodec();

    /**
     * @since 1.0
     * @param int $channelCodec Channel codec 
     */
    public function setChannelCodec($channelCodec);

    
    /**
     * @since 1.0
     * @return int Channel codec quality 
     */
    public function getChannelCodecQuality();

    /**
     * @since 1.0
     * @param int $channelCodecQuality Channel codec quality 
     */
    public function setChannelCodecQuality($channelCodecQuality);

    
    /**
     * @since 1.0
     * @return int Max clients in channel 
     */
    public function getMaxChannelClients();

    /**
     * @since 1.0
     * @param int $maxChannelClients Max clients in channel 
     */
    public function setMaxChannelClients($maxChannelClients);

    
    /**
     * @since 1.0
     * @return int Max clients in channel family
     */
    public function getMaxChannelFamilyClients();

    /**
     * @since 1.0
     * @param int $maxChannelFamilyClients Max clients in channel family 
     */
    public function setMaxChannelFamilyClients($maxChannelFamilyClients);

    
    /**
     * @since 1.0
     * @return int position of the channel 
     */
    public function getChannelPosition();

    /**
     * @since 1.0
     * @param int $channelPosition position of the channel 
     */
    public function setChannelPosition($channelPosition);

    
    /**
     * @since 1.0
     * @return bool If channel is permanent 
     */
    public function getIsPermanentChannel();

    /**
     * @since 1.0
     * @param bool $isPermanentChannel If channel is permanent 
     */
    public function setIsPermanentChannel($isPermanentChannel);

    
    /**
     * @since 1.0
     * @return bool If channel is semi-permanent 
     */
    public function getIsSemiPermanentChannel();

    /**
     * @since 1.0
     * @param bool $isSemiPermanentChannel If channel is semi-permanent 
     */
    public function setIsSemiPermanentChannel($isSemiPermanentChannel);

    
    /**
     * @since 1.0
     * @return bool If channel is temporary 
     */
    public function getIsTemporaryChannel();

    /**
     * @since 1.0
     * @param bool $isTemporaryChannel If channel is temporary 
     */
    public function setIsTemporaryChannel($isTemporaryChannel);

    
    /**
     * @since 1.0
     * @return bool If channel is default one 
     */
    public function getIsDefaultChannel();

    /**
     * @since 1.0
     * @param bool $isDefaultChannel If channel is default one 
     */
    public function setIsDefaultChannel($isDefaultChannel);

    
    /**
     * @since 1.0
     * @return bool If channel has a max clients limit 
     */
    public function getHasMaxClientsLimit();

    /**
     * @since 1.0
     * @param bool $hasMaxClientsLimit If channel has a max clients limit 
     */
    public function setHasMaxClientsLimit($hasMaxClientsLimit);

    
    /**
     * @since 1.0
     * @return bool If channel has a max family clients limit 
     */
    public function getHasMaxFamilyClientsLimit();

    /**
     * @since 1.0
     * @param bool $hasMaxFamilyClientsLimit If channel has a max family clients limit 
     */
    public function setHasMaxFamilyClientsLimit($hasMaxFamilyClientsLimit);

    
    /**
     * @since 1.0
     * @return bool If the channel inherits the max family clients from its parent channel 
     */
    public function getIsInheritingMaxFamilyClients();

    /**
     * @since 1.0
     * @param bool $isInteritingMaxFamilyClients If the channel inherits the max family clients from its parent channel 
     */
    public function setIsInheritingMaxFamilyClients($isInheritingMaxFamilyClients);

    
    /**
     * @since 1.0
     * @return bool If a specific talkpower is needed to join the channel 
     */
    public function getNeededTalkPower();

    /**
     * @since 1.0
     * @param bool $neededTalkPower If a specific talkpower is needed to join the channel  
     */
    public function setNeededTalkPower($neededTalkPower);

    
    /**
     * @since 1.0
     * @return string Phonetic name of the channel 
     */
    public function getPhoneticName();

    /**
     * @since 1.0
     * @param string $phoneticName Phonetic name of the channel 
     */
    public function setPhoneticName($phoneticName);

    
    /**
     * @since 1.0
     * @return string Filepath 
     */
    public function getFilepath();
    
    /**
     * @since 1.0
     * @param string $filepath Filepath 
     */
    public function setFilepath($filepath);

    
    /**
     * @since 1.0
     * @return bool If the channel is silenced
     */
    public function getIsSilenced();

    /**
     * @since 1.0
     * @param bool $isSilenced If the channel is silenced 
     */
    public function setIsSilenced($isSilenced);

    
    /**
     * @since 1.0
     * @return int Icon-Id 
     */
    public function getIconId();

    /**
     * @since 1.0
     * @param int $iconId Icon-Id 
     */
    public function setIconId($iconId);

    
    /**
     * @since 1.0
     * @return bool If the speech data of the channel is encrypted 
     */
    public function getIsSpeechDataEncrypted();

    /**
     * @since 1.0
     * @param bool $isSpeechDataEncrypted If the speech data of the channel is encrypted
     */
    public function setIsSpeechDataEncrypted($isSpeechDataEncrypted);

    
    /**
     * @since 1.0
     * @return int Parent channel-Id 
     */
    public function getChannelParentId();

    /**
     * @since 1.0
     * @param int $channelParentId Parent channel-Id 
     */
    public function setChannelParentId($channelParentId);

    
    /**
     * @since 1.0
     * @return int Channel-Id 
     */
    public function getChannelId();

    /**
     * @since 1.0
     * @param int $channelId Channel-Id 
     */
    public function setChannelId($channelId);
}

?>
