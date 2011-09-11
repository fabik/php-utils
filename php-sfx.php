<?php

/**
 * PHP self-extracting archive creator.
 *
 * Copyright (c) 2011, Jan-Sebastian Fabik <honza@fabik.org>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Jan-Sebastian Fabik nor the names of its contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */



/**
 * Creates an archive from the given directory.
 * @param string  the source directory path
 * @param string  the archive file path
 * @return int    the number of items archived
 */
function create_archive($dir, $archiveFile)
{
	$iterator = new RecursiveDirectoryIterator($dir);
	$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
	$count = 0;

	$zip = new ZipArchive();
	if (!$zip->open($archiveFile, ZIPARCHIVE::OVERWRITE)) {
		echo "Failed to create an archive.";
		exit(1);
	}

	foreach ($iterator as $item) {
		$path = strtr(substr($item->getPathname(), strlen($dir) + 1), '\\', '/');
		if ($item->isDir()) {
			if (!$zip->addEmptyDir($path)) {
				echo "Failed to archive a directory '$path'.";
				exit(1);
			}
		} elseif ($item->isFile()) {
			if (!$zip->addFile($item->getPathname(), $path)) {
				echo "Failed to archive a file '$path'.";
				exit(1);
			}
		}
		$count++;
	}

	if (!$zip->close()) {
		echo "Failed to create an archive.";
		exit(1);
	}

	return $count;
}



/**
 * Creates a self-extracting script.
 * @param string  the self-extracting file path
 * @param string  the archive file path
 * @param string  the output directory
 * @return void
 */
function create_sfx($sfxFile, $archiveFile, $extractDir)
{
	$afp = fopen($archiveFile, 'r');
	if (!$afp) {
		echo "Failed to open the archive file for reading.";
		exit(1);
	}
	$sfp = fopen($sfxFile, 'w');
	if (!$sfp) {
		echo "Failed to open the output file for writing.";
		exit(1);
	}
	fwrite($sfp, '<?php

$dest = ' . var_export($extractDir, true) . ';
@mkdir($dest, 0777, TRUE); // @ - directory may exist

$archiveFile = $dest . "/~temp";
$afp = fopen($archiveFile, "w");
if (!$afp) {
	echo "Failed to restore the archive.";
	exit(1);
}

$sfp = fopen(__FILE__, "r+");
if (!$sfp) {
	echo "Failed to open the current file for reading.";
	exit(1);
}

fseek($sfp, __COMPILER_HALT_OFFSET__);
while (!feof($sfp)) fwrite($afp, fread($sfp, 8192));
fclose($sfp);
fclose($afp);

$zip = new ZipArchive();
if (!$zip->open($archiveFile)) {
	echo "Failed to open the archive.";
	exit(1);
}
if (!$zip->extractTo($dest)) {
	echo "Failed to extract the archive.";
	exit(1);
}
$count = $zip->numFiles;
$zip->close();

@unlink($archiveFile); // @ - file may not exist
@unlink(__FILE__);     // @ - file may not exist

echo "$count items extracted successfully.";

__halt_compiler();');
	while (!feof($afp))
		fwrite($sfp, fread($afp, 8192));
	fclose($sfp);
	fclose($afp);
}
$options = getopt('d:o:e:');

if (!$options || !isset($options['o'])) {
	echo "Usage: php php-sfx.php [options]\n\n";
	echo "Options:\n";
	echo "    -d  the source directory (default: '.')\n";
	echo "    -o  the output file\n";
	echo "    -e  the extraction directory (default: '.')\n";
	exit;
}

$dir = realpath(isset($options['d']) ? $options['d'] : '.');
$sfxFile = $options['o'];
$archiveFile = $sfxFile . '.tmp';
$extractDir = isset($options['e']) ? $options['e'] : '.';

if ($dir === FALSE) {
	echo "The source directory does not exist.";
	exit(1);
}

@mkdir(dirname($sfxFile), 0777, TRUE);
@unlink($sfxFile);  // @ - file may not exist
@unlink($archiveFile); // @ - file may not exist

$count = create_archive($dir, $archiveFile);
create_sfx($sfxFile, $archiveFile, $extractDir);
echo "$count items packed successfully.";

@unlink($archiveFile); // @ - file may not exist
