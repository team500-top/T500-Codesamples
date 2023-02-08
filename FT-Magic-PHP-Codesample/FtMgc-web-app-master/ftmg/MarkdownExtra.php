<?php

#
namespace ftmgcMC;


# Just force ftmgcMC/Markdown.php to load. This is needed to load
# the temporary implementation class. See below for details.
\ftmgcMC\Markdown::MARKDOWNLIB_VERSION;

#
# Markdown Extra Parser Class
#
# Note: Currently the implementation resides in the temporary class
# \ftmgcMC\MarkdownExtra_TmpImpl (in the same file as \ftmgcMC\Markdown).
# This makes it easier to propagate the changes between the three different
# packaging styles of PHP Markdown. Once this issue is resolved, the
# _MarkdownExtra_TmpImpl will disappear and this one will contain the code.
#

class MarkdownExtra extends \ftmgcMC\_MarkdownExtra_TmpImpl {

	### Parser Implementation ###

	# Temporarily, the implemenation is in the _MarkdownExtra_TmpImpl class.
	# See note above.

}

