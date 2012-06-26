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
namespace devmx\Teamspeak3\Version;

/**
 *
 * @author drak3
 */
interface VersionQueryInterface
{
    const STABILITY_STABLE = 3;
    const STABILITY_BETA   = 2;
    const STABILITY_ALPHA  = 1;
    
    /**
     * Returns a Version object containing the version and build number of the most recent server release
     * @return \devmx\Teamspeak3\Version\Version the current server version
     */
    public function getCurrentServerVersion();
    
    /**
     * Return a Version object containing version and build number of the most recent client release with the given stability
     */
    public function getCurrentClientVersion($stability=static::STABILITY_STABLE);
}

?>
