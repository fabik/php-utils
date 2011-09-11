A collection of PHP utilities
=============================

PHP self-extracting archive creator
-----------------------------------

This script creates a self-extracting archive that can be extracted just by
visiting its address in a web browser. It works significantly faster than FTP,
especially for a plenty of very small files.

Usage: php php-sfx.php [options]

Options:
    -d  the source directory (default: '.')
    -o  the output file
    -e  the extraction directory (default: '.')


The following command will create a SFX archive of all files in the current
working directory. The sfx.php file will extract them also into the current
working directory.

php php-sfx.php -o sfx.php

The following command will create a SFX archive of all files in the parent
directory. The sfx.php file will extract them also into the parent directory.

php php-sfx.php -d .. -o sfx.php -e ..


PHP self-destructing script
---------------------------

This script removes all the contents of the current directory including the
file itself just by visiting its address in a web browser. It works
significantly faster than FTP, especially for a plenty of very small files.

Note: If you want to adjust the directory to be removed, just modify the $dir
variable in the script.


License
-------

Copyright (c) 2011, Jan-Sebastian Fabik <honza@fabik.org>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
  * Redistributions of source code must retain the above copyright notice,
    this list of conditions and the following disclaimer.
  * Redistributions in binary form must reproduce the above copyright notice,
    this list of conditions and the following disclaimer in the documentation
    and/or other materials provided with the distribution.
  * Neither the name of Jan-Sebastian Fabik nor the names of its contributors
    may be used to endorse or promote products derived from this software
    without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
