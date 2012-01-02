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

if($argc != 2) {
    die('not enough arguments');
}
$file = $argv[1];

$data = file_get_contents($file);

$changed = preg_replace_callback('/[\'"][a-zA-Z_]+[\'"]/' , function($match) {
    return strtolower($match[0]);
}, $data );

file_put_contents($file, $changed);


?>
